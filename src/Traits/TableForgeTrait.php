<?php
declare(strict_types=1);

namespace Fyre\Forge\Traits;

use
    Fyre\DB\ConnectionManager,
    Fyre\Forge\Exceptions\ForgeException,
    Fyre\Forge\ForgeInterface,
    Fyre\Schema\SchemaRegistry,
    Fyre\Schema\TableSchema;

use const
    ARRAY_FILTER_USE_KEY;

use function
    array_diff,
    array_diff_assoc,
    array_filter,
    array_key_exists,
    array_keys,
    array_merge,
    array_replace,
    array_search,
    array_slice,
    array_splice,
    in_array,
    is_array;

/**
 * TableForge
 */
trait TableForgeTrait
{

    protected ForgeInterface $forge;

    protected string $originalTableName;

    protected array $originalColumns = [];
    protected array $originalIndexes = [];
    protected array $originalForeignKeys = [];
    protected array $originalTableOptions = [];

    protected array $renameColumns = [];

    protected array $tableOptions;
    protected bool $tableExists;
    protected bool $dropped = false;

    /**
     * New TableForge constructor.
     * @param ForgeInterface $forge The Forge.
     * @param string $tableName The table name.
     * @param array $options The table options.
     */
    public function __construct(ForgeInterface $forge, string $tableName, array $options = [])
    {
        $this->forge = $forge;

        $connection =$this->forge->getConnection();
        $schema = SchemaRegistry::getSchema($connection);

        $clean = $options['clean'] ?? false;

        unset($options['clean']);

        parent::__construct($schema, $tableName);

        $this->originalTableName = $tableName;

        $this->tableExists = $this->schema->hasTable($this->originalTableName);

        if ($this->tableExists) {
            $this->originalTableOptions = $this->schema->table($this->tableName);
            $this->tableOptions = array_merge($this->originalTableOptions, $options);

            $this->originalColumns = $this->columns();
            $this->originalIndexes = $this->indexes();
            $this->originalForeignKeys = $this->foreignKeys();
        } else {
            $this->tableOptions = $this->forge->parseTableOptions($options);
        }

        if (!$this->tableExists || $clean) {
            $this->columns = [];
            $this->indexes = [];
            $this->foreignKeys = [];
        }
    }

    /**
     * Add a column to the table.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForgeInterface The TableForge.
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
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the foreign key already exists.
     */
    public function addForeignKey(string $foreignKey, array $options = []): static
    {
        if ($this->hasForeignKey($foreignKey)) {
            throw ForgeException::forExistingForeignKey($foreignKey);
        }

        $this->foreignKeys[$foreignKey] = $this->forge->parseForeignKeyOptions($options);

        return $this;
    }

    /**
     * Add an index to the table.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return TableForgeInterface The TableForge.
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

        $this->indexes[$index] = $this->forge->parseIndexOptions($options);

        return $this;
    }

    /**
     * Change a table column.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForgeInterface The TableForge.
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
     * Clear data from the cache.
     * @return TableForgeInterface The TableForge.
     */
    public function clear(): static
    {
        parent::clear();

        $this->renameColumns = [];

        return $this;
    }

    /**
     * Drop the table.
     * @return TableForgeInterface The TableForge.
     */
    public function drop(): static
    {
        if (!$this->tableExists) {
            throw ForgeException::forMissingTable($this->originalTableName);
        }

        $this->dropped = true;

        return $this;
    }

    /**
     * Drop a column from the table.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return TableForgeInterface The TableForge.
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
     * @return TableForgeInterface The TableForge.
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
     * @return TableForgeInterface The TableForge.
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
     * @return TableForgeInterface The TableForge.
     */
    public function execute(): static
    {
        $queries = $this->sql();

        $connection = $this->schema->getConnection();
        $connection->begin();

        foreach ($queries AS $sql) {
            $connection->query($sql);
        }

        $connection->commit();

        $this->schema->clear();

        if ($this->schema->hasTable($this->originalTableName)) {
            $this->schema->clear();
        }

        $this->clear();

        $this->originalTableName = $this->tableName;

        $this->tableExists = !$this->dropped;
        $this->dropped = false;

        if ($this->tableExists) {
            $this->originalTableOptions = $this->schema->table($this->tableName);
            $this->originalColumns = $this->columns();
            $this->originalIndexes = $this->indexes();
            $this->originalForeignKeys = $this->foreignKeys();
        } else {
            $this->originalTableOptions = [];
            $this->originalColumns = [];
            $this->originalIndexes = [];
            $this->originalForeignKeys = [];
        }

        return $this;
    }

    /**
     * Rename the table.
     * @param string $table The new table name.
     * @return TableForgeInterface The TableForge.
     */
    public function rename(string $table): static
    {
        $this->tableName = $table;

        return $this;
    }

    /**
     * Set the primary key.
     * @param string|array $columns The columns.
     * @return TableForgeInterface The TableForge.
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

        if (!$this->tableExists) {
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

        foreach ($this->originalForeignKeys AS $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->foreignKeys) && static::compare($this->foreignKeys[$foreignKey], $options)) {
                continue;
            }

            $queries[] = $this->forge->dropForeignKeySql($this->originalTableName, $foreignKey);
        }

        foreach ($this->originalIndexes AS $index => $options) {
            if (array_key_exists($index, $this->originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->indexes) && static::compare($this->indexes[$index], $options)) {
                continue;
            }

            $queries[] = $this->forge->dropIndexSql($this->originalTableName, $index);
        }

        if ($this->dropped) {
            $queries[] = $this->forge->dropTableSql($this->originalTableName);

            return $queries;
        }

        foreach ($this->originalColumns AS $column => $options) {
            $newColumn = $this->renameColumns[$column] ?? $column;

            if (array_key_exists($newColumn, $this->columns)) {
                continue;
            }

            $queries[] = $this->forge->dropColumnSql($this->originalTableName, $column);
        }

        if ($this->tableName !== $this->originalTableName) {
            $queries[] = $this->forge->renameTableSql($this->originalTableName, $this->tableName);
        }

        $tableOptions = array_diff_assoc($this->tableOptions, $this->originalTableOptions);

        if ($tableOptions !== []) {
            $queries[] = $this->forge->alterTableSql($this->tableName, $tableOptions);
        }

        $columnIndex = 0;
        $prevColumn = null;
        $originalColumns = array_keys($this->originalColumns);
        $newColumns = [];

        foreach ($this->columns AS $column => $options) {
            $originalColumn = array_search($column, $this->renameColumns) ?: $column;
            $oldIndex = array_search($column, $originalColumns);

            if ($oldIndex === false || $columnIndex !== $oldIndex) {
                if ($prevColumn) {
                    $options['after'] = $prevColumn;
                    $originalColumns = array_diff($originalColumns, [$column]);
                    $prevIndex = array_search($prevColumn, $originalColumns);
                    array_splice($originalColumns, $prevIndex + 1, 0, $column);
                } else {
                    $options['first'] = true;
                    array_unshift($originalColumns, $column);
                }
            }

            if (!array_key_exists($originalColumn, $this->originalColumns)) {
                $queries[] = $this->forge->addColumnSql($this->tableName, $column, $options);
            } else if ($columnIndex !== $oldIndex || !static::compare($options, $this->originalColumns[$originalColumn])) {
                $options['name'] = $column;
                $queries[] = $this->forge->changeColumnSql($this->tableName, $originalColumn, $options);
            }

            $newColumns[] = $column;
            $prevColumn = $column;
            $columnIndex++;
        }

        foreach ($this->indexes AS $index => $options) {
            if (array_key_exists($index, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($index, $this->originalIndexes) && static::compare($options, $this->originalIndexes[$index])) {
                continue;
            }

            $queries[] = $this->forge->addIndexSql($this->tableName, $index, $options);
        }

        foreach ($this->foreignKeys AS $foreignKey => $options) {
            if (array_key_exists($foreignKey, $this->originalForeignKeys) && static::compare($options, $this->originalForeignKeys[$foreignKey])) {
                continue;
            }


            $queries[] = $this->forge->addForeignKeySql($this->tableName, $foreignKey, $options);
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
