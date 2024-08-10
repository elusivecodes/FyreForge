<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\TableForge;

use function array_diff_assoc;
use function array_intersect_key;
use function array_key_exists;
use function array_merge;
use function array_search;

/**
 * SqliteTableTable
 */
class SqliteTableForge extends TableForge
{
    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return TableForge The TableForge.
     */
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex('primary', [
            'columns' => $columns,
            'primary' => true,
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     *
     * @throws ForgeException if a SQL operation cannot be performed due to SQLite limitations.
     */
    public function sql(): array
    {
        $generator = $this->forge->generator();

        $queries = [];

        if (!$this->tableSchema) {
            $constraints = [];
            $indexes = [];

            foreach ($this->indexes as $index => $options) {
                if ($this->hasForeignkey($index)) {
                    continue;
                }

                if ($options['primary']) {
                    $constraints[$index] = $options;
                } else {
                    $indexes[$index] = $options;
                }
            }

            $queries[] = $generator->buildCreateTable(
                $this->tableName,
                $this->columns,
                array_merge(
                    $this->tableOptions,
                    [
                        'indexes' => $constraints,
                        'foreignKeys' => $this->foreignKeys,
                    ]
                )
            );

            foreach ($indexes as $index => $options) {
                $queries[] = $generator->buildCreateIndex($this->tableName, $index, $options);
            }

            return $queries;
        }

        if ($this->dropTable) {
            $queries[] = $generator->buildDropTable($this->tableName);

            return $queries;
        }

        $tableName = $this->newTableName ?? $this->tableName;
        $originalColumns = $this->tableSchema->columns();
        $originalIndexes = $this->tableSchema->indexes();
        $originalForeignKeys = $this->tableSchema->foreignKeys();

        if ($this->tableName !== $tableName) {
            $sql = $generator->buildRenameTable($this->newTableName);
            $queries[] = $generator->buildAlterTable($this->tableName, [$sql]);
        }

        foreach ($originalForeignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->foreignKeys) && static::compare($this->foreignKeys[$foreignKey], $options)) {
                continue;
            }

            throw new ForgeException('Foreign keys cannot be dropped from SQLite tables: '.$foreignKey);
        }

        foreach ($originalIndexes as $index => $options) {
            if (array_key_exists($index, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->indexes) && static::compare($this->indexes[$index], $options)) {
                continue;
            }

            if ($options['primary'] || $options['unique']) {
                throw new ForgeException('Constraints cannot be dropped from SQLite tables: '.$index);
            }

            $queries[] = $generator->buildDropIndex($index);
        }

        foreach ($originalColumns as $column => $options) {
            $newColumn = $this->renameColumns[$column] ?? $column;

            if (array_key_exists($newColumn, $this->columns)) {
                continue;
            }

            $alterSql = $generator->buildDropColumn($column);
            $queries[] = $generator->buildAlterTable($tableName, [$alterSql]);
        }

        foreach ($this->columns as $column => $options) {
            $originalColumn = array_search($column, $this->renameColumns) ?: $column;

            if (!array_key_exists($originalColumn, $originalColumns)) {
                $alterSql = $generator->buildAddColumn($column, $options);
                $queries[] = $generator->buildAlterTable($tableName, [$alterSql]);
            } else {
                $columnOptions = array_intersect_key($options, $originalColumns[$originalColumn]);
                $columnOptions = array_diff_assoc($columnOptions, $originalColumns[$originalColumn]);

                if ($columnOptions !== []) {
                    throw new ForgeException('Columns cannot be changed in SQLite tables: '.$column);
                }

                if ($column !== $originalColumn) {
                    $sql = $generator->buildRenameColumn($originalColumn, $column);
                    $queries[] = $generator->buildAlterTable($tableName, [$sql]);
                }
            }
        }

        foreach ($this->indexes as $index => $options) {
            if (array_key_exists($index, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($index, $originalIndexes) && static::compare($options, $originalIndexes[$index])) {
                continue;
            }

            if ($options['primary']) {
                throw new ForgeException('Primary keys cannot be added to SQLite tables: '.$index);
            }

            $queries[] = $generator->buildCreateIndex($tableName, $index, $options);
        }

        foreach ($this->foreignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $originalForeignKeys) && static::compare($options, $originalForeignKeys[$foreignKey])) {
                continue;
            }

            throw new ForgeException('Foreign keys cannot be added to SQLite tables: '.$foreignKey);
        }

        return $queries;
    }
}
