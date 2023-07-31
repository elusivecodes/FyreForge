<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;
use Fyre\Schema\TableSchema;

use const ARRAY_FILTER_USE_KEY;

use function array_diff;
use function array_diff_assoc;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_replace;
use function array_search;
use function array_slice;
use function array_splice;
use function is_array;

/**
 * TableForge
 */
abstract class TableForge
{

    protected Forge $forge;
    protected Schema $schema;
    protected TableSchema|null $tableSchema = null;

    protected string $tableName;
    protected array $tableOptions;
    protected array $columns = [];
    protected array $indexes = [];
    protected array $foreignKeys = [];

    protected string|null $newTableName = null;
    protected array $renameColumns = [];
    protected bool $dropTable = false;

    /**
     * New TableForge constructor.
     * @param Forge $forge The forge.
     * @param string $tableName The table name.
     * @param array $options The table options.
     */
    public function __construct(Forge $forge, string $tableName, array $options = [])
    {
        $this->forge = $forge;

        $connection = $this->forge->getConnection();
        $this->schema = SchemaRegistry::getSchema($connection);

        $this->tableName = $tableName;

        if (!$this->schema->hasTable($this->tableName)) {
            $this->tableOptions = $this->forge->parseTableOptions($options);
            return;
        }

        $this->tableSchema = $this->schema->describe($this->tableName);
        $this->tableOptions = array_replace($this->schema->table($this->tableName), $options);
        $this->columns = $this->tableSchema->columns();
        $this->indexes = $this->tableSchema->indexes();
        $this->foreignKeys = $this->tableSchema->foreignKeys();
    }

    /**
     * Add a column to the table.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForge The TableForge.
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

        $options = $this->forge->parseColumnOptions($options);

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
     * Add a foreign key to the table.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return TableForge The TableForge.
     * @throws ForgeException if the foreign key already exists.
     */
    public function addForeignKey(string $foreignKey, array $options = []): static
    {
        if ($this->hasForeignKey($foreignKey)) {
            throw ForgeException::forExistingForeignKey($foreignKey);
        }

        $this->foreignKeys[$foreignKey] = $this->forge->parseForeignKeyOptions($options, $foreignKey);

        return $this;
    }

    /**
     * Add an index to the table.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return TableForge The TableForge.
     * @throws ForgeException if the index already exists.
     */
    public function addIndex(string $index, array $options = []): static
    {
        if ($index === 'PRIMARY') {
            $index = 'PRIMARY';
            $options['unique'] = true;
        }

        if ($this->hasIndex($index)) {
            throw ForgeException::forExistingIndex($index);
        }

        $this->indexes[$index] = $this->forge->parseIndexOptions($options, $index);

        return $this;
    }

    /**
     * Change a table column.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForge The TableForge.
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

        $options = $this->forge->parseColumnOptions($options);

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
     * Clear the column and index data.
     * @return TableForge The TableForge.
     */
    public function clear(): static
    {
        $this->columns = [];
        $this->indexes = [];
        $this->foreignKeys = [];
        $this->renameColumns = [];

        return $this;
    }

    /**
     * Get the data for a table column.
     * @param string $name The column name.
     * @return array|null The column data.
     */
    public function column(string $name): array|null
    {
        return $this->columns[$name] ?? null;
    }

    /**
     * Get the names of all table columns.
     * @return array The names of all table columns.
     */
    public function columnNames(): array
    {
        return array_keys($this->columns);
    }

    /**
     * Get the data for all table columns.
     * @return array The table columns data.
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Drop the table.
     * @return TableForge The TableForge.
     */
    public function drop(): static
    {
        if (!$this->tableSchema) {
            throw ForgeException::forMissingTable($this->tableName);
        }

        $this->dropTable = true;

        return $this;
    }

    /**
     * Drop a column from the table.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return TableForge The TableForge.
     * @throws ForgeException if the column does not exist.
     */
    public function dropColumn(string $column, array $options = []): static
    {
        if (!$this->hasColumn($column)) {
            throw ForgeException::forMissingColumn($column);
        }

        unset($this->columns[$column]);

        return $this;
    }

    /**
     * Drop a foreign key from the table.
     * @param string $foreignKey The foreign key name.
     * @return TableForge The TableForge.
     * @throws ForgeException if the foreign key does not exist.
     */
    public function dropForeignKey(string $foreignKey): static
    {
        if (!$this->hasForeignKey($foreignKey)) {
            throw ForgeException::forMissingForeignKey($foreignKey);
        }

        unset($this->foreignKeys[$foreignKey]);
        unset($this->indexes[$foreignKey]);

        return $this;
    }

    /**
     * Drop an index from the table.
     * @param string $index The index name.
     * @return TableForge The TableForge.
     * @throws ForgeException if the index does not exist.
     */
    public function dropIndex(string $index): static
    {
        if ($this->hasForeignKey($index)) {
            return $this->dropForeignKey($index);
        }

        if (!$this->hasIndex($index)) {
            throw ForgeException::forMissingIndex($index);
        }

        unset($this->indexes[$index]);

        return $this;
    }

    /**
     * Generate and execute the SQL queries.
     * @return TableForge The TableForge.
     */
    public function execute(): static
    {
        $queries = $this->sql();

        $connection = $this->forge->getConnection();
        $connection->begin();

        foreach ($queries AS $sql) {
            $connection->query($sql);
        }

        $connection->commit();

        $this->clear();

        if ($this->tableSchema) {
            $this->tableSchema->clear();
        }

        if (!$this->tableSchema || $this->newTableName || $this->dropTable) {
            $this->schema->clear();
        }

        if ($this->newTableName) {
            $this->tableName = $this->newTableName;
            $this->newTableName = null;
        }

        if ($this->dropTable) {
            $this->tableSchema = null;
            $this->dropTable = false;
        } else {
            $this->tableSchema = $this->schema->describe($this->tableName);
            $this->tableOptions = $this->schema->table($this->tableName);
            $this->columns = $this->tableSchema->columns();
            $this->indexes = $this->tableSchema->indexes();
            $this->foreignKeys = $this->tableSchema->foreignKeys();
        }

        return $this;
    }

    /**
     * Get the data for a table foreign key.
     * @param string $name The foreign key name.
     * @return array|null The foreign key data.
     */
    public function foreignKey(string $name): array|null
    {
        return $this->foreignKeys[$name] ?? null;
    }

    /**
     * Get the data for all table foreign keys.
     * @return array The table foreign keys data.
     */
    public function foreignKeys()
    {
        return $this->foreignKeys;
    }

    /**
     * Get the Forge.
     * @return Forge The Forge.
     */
    public function getForge(): Forge
    {
        return $this->forge;
    }

    /**
     * Get the table name.
     * @return string The table name.
     */
    public function getTableName(): string
    {
        return $this->newTableName ?? $this->tableName;
    }

    /**
     * Determine if the table has a column.
     * @param string $name The column name.
     * @return bool TRUE if the table has the column, otherwise FALSE.
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * Determine if the table has a foreign key.
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool
    {
        return array_key_exists($name, $this->foreignKeys);
    }

    /**
     * Determine if the table has an index.
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool
    {
        return array_key_exists($name, $this->indexes);
    }

    /**
     * Get the data for a table index.
     * @param string $name The index name.
     * @return array|null The index data.
     */
    public function index(string $name): array|null
    {
        return $this->indexes[$name] ?? null;
    }

    /**
     * Get the data for all table indexes.
     * @return array The table indexes data.
     */
    public function indexes(): array
    {
        return $this->indexes;
    }

    /**
     * Rename the table.
     * @param string $table The new table name.
     * @return TableForge The TableForge.
     */
    public function rename(string $table): static
    {
        if ($this->tableSchema) {
            $this->newTableName = $table;
        } else {
            $this->tableName = $table;
        }

        return $this;
    }

    /**
     * Set the primary key.
     * @param string|array $columns The columns.
     * @return TableForge The TableForge.
     */
    public function setPrimaryKey(string|array $columns): static
    {
        $this->addIndex('PRIMARY', [
            'columns' => $columns
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     * @return array The SQL queries.
     */
    public function sql(): array
    {
        $queries = [];

        if (!$this->tableSchema) {
            $nonForeignKeys = array_filter(
                $this->indexes,
                fn(string $index): bool => !$this->hasForeignKey($index),
                ARRAY_FILTER_USE_KEY
            );

            $queries[] = $this->forge->createTableSql(
                $this->tableName,
                $this->columns,
                array_merge(
                    $this->tableOptions,
                    [
                        'indexes' => $nonForeignKeys,
                        'foreignKeys' => $this->foreignKeys
                    ]
                )
            );

            return $queries;
        }

        $originalColumns = $this->tableSchema->columns();
        $originalIndexes = $this->tableSchema->indexes();
        $originalForeignKeys = $this->tableSchema->foreignKeys();
        $originalTableOptions = $this->schema->table($this->tableName);

        foreach ($originalForeignKeys AS $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->foreignKeys) && static::compare($this->foreignKeys[$foreignKey], $options)) {
                continue;
            }

            $queries[] = $this->forge->dropForeignKeySql($this->tableName, $foreignKey);
        }

        foreach ($originalIndexes AS $index => $options) {
            if (array_key_exists($index, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->indexes) && static::compare($this->indexes[$index], $options)) {
                continue;
            }

            $queries[] = $this->forge->dropIndexSql($this->tableName, $index);
        }

        if ($this->dropTable) {
            $queries[] = $this->forge->dropTableSql($this->tableName);

            return $queries;
        }

        foreach ($originalColumns AS $column => $options) {
            $newColumn = $this->renameColumns[$column] ?? $column;

            if (array_key_exists($newColumn, $this->columns)) {
                continue;
            }

            $queries[] = $this->forge->dropColumnSql($this->tableName, $column);
        }

        if ($this->newTableName && $this->newTableName !== $this->tableName) {
            $queries[] = $this->forge->renameTableSql($this->tableName, $this->newTableName);
        }

        $tableName = $this->newtableName ?? $this->tableName;
        $tableOptions = array_diff_assoc($this->tableOptions, $originalTableOptions);

        if ($tableOptions !== []) {
            $queries[] = $this->forge->alterTableSql($tableName, $tableOptions);
        }

        $columnIndex = 0;
        $prevColumn = null;
        $originalColumnNames = array_keys($originalColumns);
        $newColumns = [];

        foreach ($this->columns AS $column => $options) {
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
                $queries[] = $this->forge->addColumnSql($tableName, $column, $options);
            } else if ($columnIndex !== $oldIndex || !static::compare($options, $originalColumns[$originalColumn])) {
                $options['name'] = $column;
                $queries[] = $this->forge->changeColumnSql($tableName, $originalColumn, $options);
            }

            $newColumns[] = $column;
            $prevColumn = $column;
            $columnIndex++;
        }

        foreach ($this->indexes AS $index => $options) {
            if (array_key_exists($index, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($index, $originalIndexes) && static::compare($options, $originalIndexes[$index])) {
                continue;
            }

            $queries[] = $this->forge->addIndexSql($tableName, $index, $options);
        }

        foreach ($this->foreignKeys AS $foreignKey => $options) {
            if (array_key_exists($foreignKey, $originalForeignKeys) && static::compare($options, $originalForeignKeys[$foreignKey])) {
                continue;
            }


            $queries[] = $this->forge->addForeignKeySql($tableName, $foreignKey, $options);
        }

        return $queries;
    }

    /**
     * Compare the difference in array options.
     * @param array $a The array.
     * @param array $b The array to compare against.
     * @return bool TRUE if the arrays are equal, otherwise FALSE.
     */
    protected static function compare(array $a, array $b): bool
    {
        foreach ($a AS $key => $value) {
            if (!array_key_exists($key, $b)) {
                continue;
            }

            if ($value == $b[$key]) {
                continue;
            }

            if (is_array($value) && static::compare($value, (array) $b[$key])) {
                continue;
            }

            return false;
        }

        return true;
    }

}
