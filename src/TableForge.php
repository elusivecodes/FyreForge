<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;
use Fyre\Schema\TableSchema;

use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_replace;
use function is_array;

/**
 * TableForge
 */
abstract class TableForge
{
    protected array $columns = [];

    protected bool $dropTable = false;

    protected array $foreignKeys = [];

    protected Forge $forge;

    protected array $indexes = [];

    protected string|null $newTableName = null;

    protected array $renameColumns = [];

    protected Schema $schema;

    protected string $tableName;

    protected array $tableOptions;

    protected TableSchema|null $tableSchema = null;

    /**
     * New TableForge constructor.
     *
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
            $this->tableOptions = $this->forge->generator()->parseTableOptions($options);

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

        $this->columns[$column] = $this->forge->generator()->parseColumnOptions($options);

        return $this;
    }

    /**
     * Add a foreign key to the table.
     *
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return TableForge The TableForge.
     *
     * @throws ForgeException if the foreign key already exists.
     */
    public function addForeignKey(string $foreignKey, array $options = []): static
    {
        if ($this->hasForeignKey($foreignKey)) {
            throw ForgeException::forExistingForeignKey($foreignKey);
        }

        $this->foreignKeys[$foreignKey] = $this->forge->generator()->parseForeignKeyOptions($options, $foreignKey);

        return $this;
    }

    /**
     * Add an index to the table.
     *
     * @param string $index The index name.
     * @param array $options The index options.
     * @return TableForge The TableForge.
     *
     * @throws ForgeException if the index already exists.
     */
    public function addIndex(string $index, array $options = []): static
    {
        if ($this->hasIndex($index)) {
            throw ForgeException::forExistingIndex($index);
        }

        $this->indexes[$index] = $this->forge->generator()->parseIndexOptions($options, $index);

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

        unset($options['name']);

        $oldOptions = $this->columns[$column];
        if (array_key_exists('type', $options) && $options['type'] !== $this->columns[$column]['type']) {
            $oldOptions['length'] = null;
        }

        $options = array_replace($oldOptions, $options);

        $options = $this->forge->generator()->parseColumnOptions($options);

        if ($newColumn !== $column) {
            $this->renameColumns[$column] = $newColumn;
            unset($this->columns[$column]);
        }

        $this->columns[$newColumn] = $options;

        return $this;
    }

    /**
     * Clear the column and index data.
     *
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
     *
     * @param string $name The column name.
     * @return array|null The column data.
     */
    public function column(string $name): array|null
    {
        return $this->columns[$name] ?? null;
    }

    /**
     * Get the names of all table columns.
     *
     * @return array The names of all table columns.
     */
    public function columnNames(): array
    {
        return array_keys($this->columns);
    }

    /**
     * Get the data for all table columns.
     *
     * @return array The table columns data.
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Drop the table.
     *
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
     *
     * @param string $column The column name.
     * @return TableForge The TableForge.
     *
     * @throws ForgeException if the column does not exist.
     */
    public function dropColumn(string $column): static
    {
        if (!$this->hasColumn($column)) {
            throw ForgeException::forMissingColumn($column);
        }

        unset($this->columns[$column]);

        return $this;
    }

    /**
     * Drop a foreign key from the table.
     *
     * @param string $foreignKey The foreign key name.
     * @return TableForge The TableForge.
     *
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
     *
     * @param string $index The index name.
     * @return TableForge The TableForge.
     *
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
     *
     * @return TableForge The TableForge.
     */
    public function execute(): static
    {
        $queries = $this->sql();

        $connection = $this->forge->getConnection();

        foreach ($queries as $sql) {
            $connection->query($sql);
        }

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
     *
     * @param string $name The foreign key name.
     * @return array|null The foreign key data.
     */
    public function foreignKey(string $name): array|null
    {
        return $this->foreignKeys[$name] ?? null;
    }

    /**
     * Get the data for all table foreign keys.
     *
     * @return array The table foreign keys data.
     */
    public function foreignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Get the Forge.
     *
     * @return Forge The Forge.
     */
    public function getForge(): Forge
    {
        return $this->forge;
    }

    /**
     * Get the table name.
     *
     * @return string The table name.
     */
    public function getTableName(): string
    {
        return $this->newTableName ?? $this->tableName;
    }

    /**
     * Determine if the table has a column.
     *
     * @param string $name The column name.
     * @return bool TRUE if the table has the column, otherwise FALSE.
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * Determine if the table has a foreign key.
     *
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool
    {
        return array_key_exists($name, $this->foreignKeys);
    }

    /**
     * Determine if the table has an index.
     *
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool
    {
        return array_key_exists($name, $this->indexes);
    }

    /**
     * Get the data for a table index.
     *
     * @param string $name The index name.
     * @return array|null The index data.
     */
    public function index(string $name): array|null
    {
        return $this->indexes[$name] ?? null;
    }

    /**
     * Get the data for all table indexes.
     *
     * @return array The table indexes data.
     */
    public function indexes(): array
    {
        return $this->indexes;
    }

    /**
     * Rename the table.
     *
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
     *
     * @param array|string $columns The columns.
     * @return TableForge The TableForge.
     */
    abstract public function setPrimaryKey(array|string $columns): static;

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     */
    abstract public function sql(): array;

    /**
     * Compare the difference in array options.
     *
     * @param array $a The array.
     * @param array $b The array to compare against.
     * @return bool TRUE if the arrays are equal, otherwise FALSE.
     */
    protected static function compare(array $a, array $b): bool
    {
        foreach ($a as $key => $value) {
            if (!array_key_exists($key, $b)) {
                continue;
            }

            if ($value === $b[$key]) {
                continue;
            }

            if (is_array($value) && is_array($b) && array_diff($a, $b) === [] && array_diff($b, $a) === []) {
                continue;
            }

            return false;
        }

        return true;
    }
}
