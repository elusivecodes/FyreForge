<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\TableForge;

use function array_diff_assoc;
use function array_intersect_key;
use function array_key_exists;
use function array_merge;
use function array_search;

/**
 * PostgresTableTable
 */
class PostgresTableForge extends TableForge
{
    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return TableForge The TableForge.
     */
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex($this->tableName.'_pkey', [
            'columns' => $columns,
            'primary' => true,
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
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

                if ($options['primary'] || $options['unique']) {
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

            if ($this->tableOptions['comment']) {
                $queries[] = $generator->buildCommentOnTable($this->tableName, $this->tableOptions['comment']);
            }

            return $queries;
        }

        if ($this->dropTable) {
            $queries[] = $generator->buildDropTable($this->tableName);

            return $queries;
        }

        $commentQueries = [];
        $indexQueries = [];
        $statements = [];
        $incrementStatements = [];

        $tableName = $this->newTableName ?? $this->tableName;
        $originalColumns = $this->tableSchema->columns();
        $originalIndexes = $this->tableSchema->indexes();
        $originalForeignKeys = $this->tableSchema->foreignKeys();
        $originalTableOptions = $this->schema->table($this->tableName);

        if ($this->tableName !== $tableName) {
            $sql = $generator->buildRenameTable($this->newTableName);
            $queries[] = $generator->buildAlterTable($this->tableName, [$sql]);
        }

        $tableOptions = array_intersect_key($this->tableOptions, $originalTableOptions);
        $tableOptions = array_diff_assoc($tableOptions, $originalTableOptions);

        if (array_key_exists('comment', $tableOptions)) {
            $queries[] = $generator->buildCommentOnTable($tableName, $tableOptions['comment']);
        }

        foreach ($originalForeignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->foreignKeys) && static::compare($this->foreignKeys[$foreignKey], $options)) {
                continue;
            }

            $statements[] = $generator->buildDropConstraint($foreignKey);
        }

        foreach ($originalIndexes as $index => $options) {
            if (array_key_exists($index, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->indexes) && static::compare($this->indexes[$index], $options)) {
                continue;
            }

            if ($options['primary'] || $options['unique']) {
                $statements[] = $generator->buildDropConstraint($index);
            } else {
                $queries[] = $generator->buildDropIndex($index);
            }
        }

        foreach ($originalColumns as $column => $options) {
            $newColumn = $this->renameColumns[$column] ?? $column;

            if (array_key_exists($newColumn, $this->columns)) {
                continue;
            }

            $statements[] = $generator->buildDropColumn($column);
        }

        foreach ($this->columns as $column => $options) {
            $originalColumn = array_search($column, $this->renameColumns) ?: $column;

            if (!array_key_exists($originalColumn, $originalColumns)) {
                $statements[] = $generator->buildAddColumn($column, $options);
            } else {
                $columnOptions = array_intersect_key($options, $originalColumns[$originalColumn]);
                $columnOptions = array_diff_assoc($columnOptions, $originalColumns[$originalColumn]);

                if ($column !== $originalColumn) {
                    $sql = $generator->buildRenameColumn($originalColumn, $column);
                    $queries[] = $generator->buildAlterTable($tableName, [$sql]);
                }

                if (
                    array_key_exists('type', $columnOptions) ||
                    array_key_exists('length', $columnOptions) ||
                    array_key_exists('precision', $columnOptions)
                ) {

                    $statements[] = $generator->buildAlterColumnType($column, $options + ['cast' => array_key_exists('type', $columnOptions)]);
                }

                if (array_key_exists('nullable', $columnOptions)) {
                    $statements[] = $generator->buildAlterColumnNullable($column, $columnOptions['nullable']);
                }

                if (array_key_exists('default', $columnOptions)) {
                    $statements[] = $generator->buildAlterColumnDefault($column, $columnOptions['default']);
                }

                if (array_key_exists('autoIncrement', $columnOptions)) {
                    $incrementStatements[] = $generator->buildAlterColumnAutoIncrement($column, $columnOptions['autoIncrement']);
                }

                if (array_key_exists('comment', $columnOptions)) {
                    $commentQueries[] = $generator->buildCommentOnColumn($tableName, $column, $columnOptions['comment']);
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

            if ($options['primary'] || $options['unique']) {
                $statements[] = $generator->buildAddConstraint($index, $options);
            } else {
                $indexQueries[] = $generator->buildCreateIndex($tableName, $index, $options);
            }
        }

        foreach ($this->foreignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $originalForeignKeys) && static::compare($options, $originalForeignKeys[$foreignKey])) {
                continue;
            }

            $statements[] = $generator->buildAddForeignKey($foreignKey, $options);
        }

        $statements = array_merge($statements, $incrementStatements);

        if ($statements !== []) {
            $queries[] = $generator->buildAlterTable($tableName, $statements);
        }

        return array_merge($queries, $indexQueries, $commentQueries);
    }
}
