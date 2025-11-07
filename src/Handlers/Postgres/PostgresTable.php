<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\Table;
use Override;

use function array_key_exists;
use function array_merge;
use function array_search;

/**
 * PostgresTable
 */
class PostgresTable extends Table
{
    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return PostgresTable The PostgresTable.
     */
    #[Override]
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex($this->name.'_pkey', [
            'columns' => (array) $columns,
            'primary' => true,
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     */
    #[Override]
    public function sql(): array
    {
        $generator = $this->forge->generator();

        $queries = [];

        if (!$this->schemaTable) {
            $queries[] = $generator->buildCreateTable($this);

            foreach ($this->indexes as $name => $index) {
                if ($index->isPrimary() || $index->isUnique() || array_key_exists($name, $this->foreignKeys)) {
                    continue;
                }

                $queries[] = $generator->buildCreateIndex($index);
            }

            if ($this->comment) {
                $queries[] = $generator->buildCommentOnTable($this);
            }

            return $queries;
        }

        if ($this->dropTable) {
            $queries[] = $generator->buildDropTable($this->name);

            return $queries;
        }

        $tableName = $this->getName();

        $commentQueries = [];
        $indexQueries = [];
        $statements = [];
        $incrementStatements = [];

        $originalColumns = $this->schemaTable->columns()->toArray();
        $originalIndexes = $this->schemaTable->indexes()->toArray();
        $originalForeignKeys = $this->schemaTable->foreignKeys()->toArray();

        if ($this->name !== $tableName) {
            $sql = $generator->buildRenameTable($this->newName);
            $queries[] = $generator->buildAlterTable($this->name, [$sql]);
        }

        if ($this->comment !== $this->schemaTable->getComment()) {
            $queries[] = $generator->buildCommentOnTable($this);
        }

        foreach ($originalForeignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $this->foreignKeys) && $this->foreignKeys[$name]->compare($foreignKey)) {
                continue;
            }

            $statements[] = $generator->buildDropConstraint($name);
        }

        foreach ($originalIndexes as $name => $index) {
            if (array_key_exists($name, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($name, $this->indexes) && $this->indexes[$name]->compare($index)) {
                continue;
            }

            if ($index->isPrimary() || $index->isUnique()) {
                $statements[] = $generator->buildDropConstraint($name);
            } else {
                $queries[] = $generator->buildDropIndex($name);
            }
        }

        foreach ($originalColumns as $name => $column) {
            $newName = $this->renameColumns[$name] ?? $name;

            if (array_key_exists($newName, $this->columns)) {
                continue;
            }

            $statements[] = $generator->buildDropColumn($name);
        }

        foreach ($this->columns as $name => $column) {
            $originalName = array_search($name, $this->renameColumns) ?: $name;

            if (!array_key_exists($originalName, $originalColumns)) {
                $statements[] = $generator->buildAddColumn($column);

                if ($column->getComment()) {
                    $commentQueries[] = $generator->buildCommentOnColumn($column);
                }
            } else {
                $originalColumn = $originalColumns[$originalName];

                if ($name !== $originalName) {
                    $sql = $generator->buildRenameColumn($originalName, $name);
                    $queries[] = $generator->buildAlterTable($tableName, [$sql]);
                }

                if (
                    $column->getType() !== $originalColumn->getType() ||
                    $column->getLength() !== $originalColumn->getLength() ||
                    $column->getPrecision() !== $originalColumn->getPrecision()
                ) {
                    $statements[] = $generator->buildAlterColumnType($column, [
                        'cast' => $column->getType() !== $originalColumn->getType(),
                    ]);
                }

                if ($column->isNullable() !== $originalColumn->isNullable()) {
                    $statements[] = $generator->buildAlterColumnNullable($column);
                }

                if ($column->getDefault() !== $originalColumn->getDefault()) {
                    $statements[] = $generator->buildAlterColumnDefault($column);
                }

                if ($column->isAutoIncrement() !== $originalColumn->isAutoIncrement()) {
                    $incrementStatements[] = $generator->buildAlterColumnAutoIncrement($column);
                }

                if ($column->getComment() !== $originalColumn->getComment()) {
                    $commentQueries[] = $generator->buildCommentOnColumn($column);
                }
            }
        }

        foreach ($this->indexes as $name => $index) {
            if (array_key_exists($name, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($name, $originalIndexes) && $index->compare($originalIndexes[$name])) {
                continue;
            }

            if ($index->isPrimary() || $index->isUnique()) {
                $statements[] = $generator->buildAddConstraint($index);
            } else {
                $indexQueries[] = $generator->buildCreateIndex($index);
            }
        }

        foreach ($this->foreignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $originalForeignKeys) && $foreignKey->compare($originalForeignKeys[$name])) {
                continue;
            }

            $statements[] = $generator->buildAddForeignKey($foreignKey);
        }

        $statements = array_merge($statements, $incrementStatements);

        if ($statements !== []) {
            $queries[] = $generator->buildAlterTable($tableName, $statements);
        }

        return array_merge($queries, $indexQueries, $commentQueries);
    }

    /**
     * Build a Column.
     *
     * @param string $name The column name.
     * @param array $data The column data.
     * @return PostgresColumn The Column.
     */
    #[Override]
    protected function buildColumn(string $name, array $data): PostgresColumn
    {
        return $this->container->build(PostgresColumn::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Build an Index.
     *
     * @param string $name The index key name.
     * @param array $data The index key data.
     * @return PostgresIndex The Index.
     */
    #[Override]
    protected function buildIndex(string $name, array $data): PostgresIndex
    {
        return $this->container->build(PostgresIndex::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }
}
