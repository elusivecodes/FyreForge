<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\TableForge;

use function array_diff;
use function array_diff_assoc;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_replace;
use function array_search;
use function array_slice;
use function array_splice;
use function array_unshift;

/**
 * MysqlTableTable
 */
class MysqlTableForge extends TableForge
{
    /**
     * Add a column to the table.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForge The TableForge.
     *
     * @throws ForgeException if the column already exists.
     */
    public function addColumn(string $column, array $options = []): static
    {
        if ($this->hasColumn($column)) {
            throw ForgeException::forExistingColumn($column);
        }

        $after = $options['after'] ?? null;
        $first = $options['first'] ?? false;

        unset($options['after']);
        unset($options['first']);

        $options['charset'] ??= $this->tableOptions['charset'];
        $options['collation'] ??= $this->tableOptions['collation'];

        $options = $this->forge->generator()->parseColumnOptions($options);

        if ($first) {
            $this->columns = array_merge([$column => $options], $this->columns);
        } else if ($after) {
            $afterIndex = array_search($after, array_keys($this->columns));

            $beforeColumns = array_slice($this->columns, 0, $afterIndex);
            $afterColumns = array_slice($this->columns, $afterIndex);

            $this->columns = array_merge($beforeColumns, [$column => $options], $afterColumns);
        } else {
            $this->columns[$column] = $options;
        }

        return $this;
    }

    /**
     * Change a table column.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForge The TableForge.
     *
     * @throws ForgeException if the column does not exist.
     */
    public function changeColumn(string $column, array $options): static
    {
        if (!$this->hasColumn($column)) {
            throw ForgeException::forMissingColumn($column);
        }

        $newColumn = $options['name'] ?? $column;
        $after = $options['after'] ?? null;

        unset($options['name']);
        unset($options['after']);

        $oldOptions = $this->columns[$column];
        if (array_key_exists('type', $options) && $options['type'] !== $this->columns[$column]['type']) {
            $oldOptions['length'] = null;
        }

        $options = array_replace($oldOptions, $options);

        $options = $this->forge->generator()->parseColumnOptions($options);

        if ($newColumn !== $column) {
            $after ??= $column;
            $this->renameColumns[$column] = $newColumn;
        }

        if ($after) {
            $afterIndex = array_search($after, array_keys($this->columns));

            $beforeColumns = array_slice($this->columns, 0, $afterIndex);
            $afterColumns = array_slice($this->columns, $afterIndex);

            $this->columns = array_merge($beforeColumns, [$newColumn => $options], $afterColumns);
        } else {
            $this->columns[$newColumn] = $options;
        }

        if ($newColumn !== $column) {
            unset($this->columns[$column]);
        }

        return $this;
    }

    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return TableForge The TableForge.
     */
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex('PRIMARY', [
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

        if (!$this->tableSchema) {
            $nonForeignKeys = [];

            foreach ($this->indexes as $index => $options) {
                if ($this->hasForeignkey($index)) {
                    continue;
                }

                $nonForeignKeys[$index] = $options;
            }

            $query = $generator->buildCreateTable(
                $this->tableName,
                $this->columns,
                array_merge(
                    $this->tableOptions,
                    [
                        'indexes' => $nonForeignKeys,
                        'foreignKeys' => $this->foreignKeys,
                    ]
                )
            );

            return [$query];
        }

        if ($this->dropTable) {
            $query = $generator->buildDropTable($this->tableName);

            return [$query];
        }

        $originalColumns = $this->tableSchema->columns();
        $originalIndexes = $this->tableSchema->indexes();
        $originalForeignKeys = $this->tableSchema->foreignKeys();
        $originalTableOptions = $this->schema->table($this->tableName);

        $statements = [];

        $tableOptions = array_intersect_key($this->tableOptions, $originalTableOptions);
        $tableOptions = array_diff_assoc($tableOptions, $originalTableOptions);

        if ($tableOptions !== []) {
            $forceComment = array_key_exists('comment', $tableOptions);
            $statements[] = $generator->buildTableOptions($tableOptions);
        }

        foreach ($originalForeignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->foreignKeys) && static::compare($this->foreignKeys[$foreignKey], $options)) {
                continue;
            }

            $statements[] = $generator->buildDropForeignKey($foreignKey);
        }

        foreach ($originalIndexes as $index => $options) {
            if (array_key_exists($index, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->indexes) && static::compare($this->indexes[$index], $options)) {
                continue;
            }

            $statements[] = $generator->buildDropIndex($index);
        }

        $originalColumnNames = [];
        foreach ($originalColumns as $column => $options) {
            $newColumn = $this->renameColumns[$column] ?? $column;

            if (array_key_exists($newColumn, $this->columns)) {
                $originalColumnNames[] = $newColumn;

                continue;
            }

            $statements[] = $generator->buildDropColumn($column);
        }

        $columnIndex = 0;
        $prevColumn = null;
        $newColumns = [];

        foreach ($this->columns as $column => $options) {
            $originalColumn = array_search($column, $this->renameColumns) ?: $column;
            $oldIndex = array_search($column, $originalColumnNames);

            if ($oldIndex === false || $columnIndex !== $oldIndex) {
                if ($prevColumn) {
                    $options['after'] = $prevColumn;
                    $originalColumnNames = array_diff($originalColumnNames, [$column]);
                    $prevIndex = array_search($prevColumn, $originalColumnNames);
                    array_splice($originalColumnNames, $prevIndex + 1, 0, $column);
                } else {
                    $options['first'] = true;
                    array_unshift($originalColumnNames, $column);
                }
            }

            if (!array_key_exists($originalColumn, $originalColumns)) {
                $statements[] = $generator->buildAddColumn($column, $options);
            } else if ($column !== $originalColumn || $columnIndex !== $oldIndex || !static::compare($options, $originalColumns[$originalColumn])) {
                $options['name'] = $column;
                $forceComment = $options['comment'] !== $originalColumns[$originalColumn]['comment'];
                $statements[] = $generator->buildChangeColumn($originalColumn, $options, $forceComment);
            }

            $newColumns[] = $column;
            $prevColumn = $column;
            $columnIndex++;
        }

        foreach ($this->indexes as $index => $options) {
            if (array_key_exists($index, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($index, $originalIndexes) && static::compare($options, $originalIndexes[$index])) {
                continue;
            }

            $statements[] = $generator->buildAddIndex($index, $options);
        }

        foreach ($this->foreignKeys as $foreignKey => $options) {
            if (array_key_exists($foreignKey, $originalForeignKeys) && static::compare($options, $originalForeignKeys[$foreignKey])) {
                continue;
            }

            $statements[] = $generator->buildAddForeignKey($foreignKey, $options);
        }

        if ($this->newTableName && $this->newTableName !== $this->tableName) {
            $statements[] = $generator->buildRenameTable($this->newTableName);
        }

        if ($statements === []) {
            return [];
        }

        $query = $generator->buildAlterTable($this->tableName, $statements);

        return [$query];
    }
}
