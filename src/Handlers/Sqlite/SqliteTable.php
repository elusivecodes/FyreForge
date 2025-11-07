<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\Table;
use Override;

use function array_key_exists;
use function array_search;

/**
 * SqliteTable
 */
class SqliteTable extends Table
{
    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return SqliteTable The Table.
     */
    #[Override]
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex('primary', [
            'columns' => (array) $columns,
            'primary' => true,
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     *
     * @throws ForgeException if a SQL operation cannot be performed.
     */
    #[Override]
    public function sql(): array
    {
        $generator = $this->forge->generator();

        $queries = [];

        if (!$this->schemaTable) {
            $queries[] = $generator->buildCreateTable($this);

            foreach ($this->indexes as $name => $index) {
                if ($index->isPrimary() || array_key_exists($name, $this->foreignKeys)) {
                    continue;
                }

                $queries[] = $generator->buildCreateIndex($index);
            }

            return $queries;
        }

        if ($this->dropTable) {
            $queries[] = $generator->buildDropTable($this->name);

            return $queries;
        }

        $tableName = $this->getName();

        $originalColumns = $this->schemaTable->columns()->toArray();
        $originalIndexes = $this->schemaTable->indexes()->toArray();
        $originalForeignKeys = $this->schemaTable->foreignKeys()->toArray();

        if ($this->name !== $tableName) {
            $sql = $generator->buildRenameTable($this->newName);
            $queries[] = $generator->buildAlterTable($this->name, [$sql]);
        }

        foreach ($originalForeignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $this->foreignKeys) && $this->foreignKeys[$name]->compare($foreignKey)) {
                continue;
            }

            throw new ForgeException('Foreign keys cannot be dropped from SQLite tables: '.$name);
        }

        foreach ($originalIndexes as $name => $index) {
            if (array_key_exists($name, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($name, $this->indexes) && $this->indexes[$name]->compare($index)) {
                continue;
            }

            if ($index->isPrimary()) {
                throw new ForgeException('Primary keys cannot be dropped from SQLite tables: '.$name);
            }

            $queries[] = $generator->buildDropIndex($name);
        }

        foreach ($originalColumns as $name => $column) {
            $newName = $this->renameColumns[$name] ?? $name;

            if (array_key_exists($newName, $this->columns)) {
                continue;
            }

            $alterSql = $generator->buildDropColumn($name);
            $queries[] = $generator->buildAlterTable($tableName, [$alterSql]);
        }

        foreach ($this->columns as $name => $column) {
            $originalName = array_search($name, $this->renameColumns) ?: $name;

            if (!array_key_exists($originalName, $originalColumns)) {
                $alterSql = $generator->buildAddColumn($column);
                $queries[] = $generator->buildAlterTable($tableName, [$alterSql]);
            } else {
                if (!$column->compare($originalColumns[$originalName])) {
                    throw new ForgeException('Columns cannot be changed in SQLite tables: '.$name);
                }

                if ($name !== $originalName) {
                    $sql = $generator->buildRenameColumn($originalName, $name);
                    $queries[] = $generator->buildAlterTable($tableName, [$sql]);
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

            if ($index->isPrimary()) {
                throw new ForgeException('Primary keys cannot be added to SQLite tables: '.$name);
            }

            $queries[] = $generator->buildCreateIndex($index);
        }

        foreach ($this->foreignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $originalForeignKeys) && $foreignKey->compare($originalForeignKeys[$name])) {
                continue;
            }

            throw new ForgeException('Foreign keys cannot be added to SQLite tables: '.$name);
        }

        return $queries;
    }

    /**
     * Build a Column.
     *
     * @param string $name The column name.
     * @param array $data The column data.
     * @return SqliteColumn The Column.
     */
    #[Override]
    protected function buildColumn(string $name, array $data): SqliteColumn
    {
        return $this->container->build(SqliteColumn::class, [
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
     * @return SqliteIndex The Index.
     */
    #[Override]
    protected function buildIndex(string $name, array $data): SqliteIndex
    {
        return $this->container->build(SqliteIndex::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }
}
