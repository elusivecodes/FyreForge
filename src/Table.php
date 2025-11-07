<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Container\Container;
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Schema\Column as SchemaColumn;
use Fyre\Schema\ForeignKey as SchemaForeignKey;
use Fyre\Schema\Index as SchemaIndex;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;
use Fyre\Schema\Table as SchemaTable;
use Fyre\Utility\Traits\MacroTrait;

use function array_diff_key;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_replace;
use function array_search;
use function array_slice;
use function get_object_vars;

/**
 * Table
 */
abstract class Table
{
    use MacroTrait;

    protected array $columns = [];

    protected bool $dropTable = false;

    protected array $foreignKeys = [];

    protected array $indexes = [];

    protected string|null $newName = null;

    protected array $renameColumns = [];

    protected Schema $schema;

    protected SchemaTable|null $schemaTable = null;

    /**
     * New Table constructor.
     *
     * @param Container $container The Container.
     * @param Forge $forge The forge.
     * @param string $name The table name.
     * @param SchemaRegistry The SchemaRegistry.
     * @param array $options The table options.
     */
    public function __construct(
        protected Container $container,
        protected Forge $forge,
        SchemaRegistry $schemaRegistry,
        protected string $name,
        protected string|null $comment = null,
    ) {
        $connection = $this->forge->getConnection();
        $this->schema = $schemaRegistry->use($connection);

        if (!$this->schema->hasTable($this->name)) {
            return;
        }

        $this->reloadSchema();
    }

    /**
     * Get the debug info of the object.
     *
     * @return array The debug info.
     */
    public function __debugInfo(): array
    {
        $data = get_object_vars($this);

        unset($data['container']);
        unset($data['forge']);
        unset($data['schema']);
        unset($data['schemaTable']);

        return $data;
    }

    /**
     * Add a column to the table.
     *
     * @param string $name The column name.
     * @param array $options The column options.
     * @return Table The Table.
     *
     * @throws ForgeException if the column already exists.
     */
    public function addColumn(string $name, array $options = []): static
    {
        if ($this->hasColumn($name)) {
            throw ForgeException::forExistingColumn($name);
        }

        $this->columns[$name] = $this->buildColumn($name, $options);

        return $this;
    }

    /**
     * Add a foreign key to the table.
     *
     * @param string $name The foreign key name.
     * @param array $options The foreign key options.
     * @return Table The Table.
     *
     * @throws ForgeException if the foreign key already exists.
     */
    public function addForeignKey(string $name, array $options = []): static
    {
        if ($this->hasForeignKey($name)) {
            throw ForgeException::forExistingForeignKey($name);
        }

        $options['columns'] ??= $name;

        $this->foreignKeys[$name] = $this->buildForeignKey($name, $options);

        return $this;
    }

    /**
     * Add an index to the table.
     *
     * @param string $name The index name.
     * @param array $options The index options.
     * @return Table The Table.
     *
     * @throws ForgeException if the index already exists.
     */
    public function addIndex(string $name, array $options = []): static
    {
        if ($this->hasIndex($name)) {
            throw ForgeException::forExistingIndex($name);
        }

        $options['columns'] ??= $name;

        $this->indexes[$name] = $this->buildIndex($name, $options);

        return $this;
    }

    /**
     * Change a table column.
     *
     * @param string $name The column name.
     * @param array $options The column options.
     * @return Table The Table.
     *
     * @throws ForgeException if the column does not exist.
     */
    public function changeColumn(string $name, array $options): static
    {
        if (!$this->hasColumn($name)) {
            throw ForgeException::forMissingColumn($name);
        }

        $newName = $options['name'] ?? $name;
        $oldOptions = $this->columns[$name]->toArray();

        unset($options['name']);
        unset($oldOptions['name']);

        if (array_key_exists('type', $options) && $options['type'] !== $oldOptions['type']) {
            $options['length'] ??= null;
        }

        $options = array_replace($oldOptions, $options);

        $this->columns[$newName] = $this->buildColumn($newName, $options);

        if ($newName !== $name) {
            $this->renameColumns[$name] = $newName;
            static::updateColumnOrder($newName, after: $name);
            unset($this->columns[$name]);
        }

        return $this;
    }

    /**
     * Clear the column and index data.
     *
     * @return Table The Table.
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
     * Get a table column.
     *
     * @param string $name The column name.
     * @return Column The Column.
     */
    public function column(string $name): Column
    {
        if (!array_key_exists($name, $this->columns)) {
            throw ForgeException::forInvalidColumn($this->name, $name);
        }

        return $this->columns[$name];
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
     * Get all table columns.
     *
     * @return array The table columns data.
     */
    public function columns(): array
    {
        return $this->columns;
    }

    /**
     * Determine whether this table is equivalent to a Schema Table.
     *
     * @param SchemaTable $schemaTable The Schema Table.
     * @return bool TRUE if the tables are equivalent, otherwise FALSE.
     */
    public function compare(SchemaTable $schemaTable): bool
    {
        return $this->comment === $schemaTable->getComment();
    }

    /**
     * Drop the table.
     *
     * @return Table The Table.
     */
    public function drop(): static
    {
        if (!$this->schemaTable) {
            throw ForgeException::forMissingTable($this->name);
        }

        $this->dropTable = true;

        return $this;
    }

    /**
     * Drop a column from the table.
     *
     * @param string $column The column name.
     * @return Table The Table.
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
     * @return Table The Table.
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
     * @return Table The Table.
     *
     * @throws ForgeException if the index does not exist.
     */
    public function dropIndex(string $index): static
    {
        if (!$this->hasIndex($index)) {
            throw ForgeException::forMissingIndex($index);
        }

        unset($this->foreignKeys[$index]);
        unset($this->indexes[$index]);

        return $this;
    }

    /**
     * Generate and execute the SQL queries.
     *
     * @return Table The Table.
     */
    public function execute(): static
    {
        $queries = $this->sql();

        $connection = $this->forge->getConnection();

        foreach ($queries as $sql) {
            $connection->query($sql);
        }

        $this->clear();

        if ($this->schemaTable) {
            $this->schemaTable->clear();
        }

        if (
            !$this->schemaTable ||
            $this->newName ||
            $this->dropTable ||
            !$this->compare($this->schemaTable)
        ) {
            $this->schema->clear();
        }

        if ($this->newName) {
            $this->name = $this->newName;
            $this->newName = null;
        }

        if ($this->dropTable) {
            $this->schemaTable = null;
            $this->dropTable = false;
        } else {
            $this->reloadSchema(true);
        }

        return $this;
    }

    /**
     * Get a table foreign key.
     *
     * @param string $name The foreign key name.
     * @return ForeignKey The ForeignKey.
     */
    public function foreignKey(string $name): ForeignKey
    {
        if (!array_key_exists($name, $this->foreignKeys)) {
            throw ForgeException::forInvalidForeignKey($this->name, $name);
        }

        return $this->foreignKeys[$name];
    }

    /**
     * Get all table foreign keys.
     *
     * @return array The table foreign keys data.
     */
    public function foreignKeys(): array
    {
        return $this->foreignKeys;
    }

    /**
     * Get the table comment.
     *
     * @return string|null The table comment.
     */
    public function getComment(): string|null
    {
        return $this->comment;
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
    public function getName(): string
    {
        return $this->newName ?? $this->name;
    }

    /**
     * Determine whether the table has a column.
     *
     * @param string $name The column name.
     * @return bool TRUE if the table has the column, otherwise FALSE.
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * Determine whether the table has a foreign key.
     *
     * @param string $name The foreign key name.
     * @return bool TRUE if the table has the foreign key, otherwise FALSE.
     */
    public function hasForeignKey(string $name): bool
    {
        return array_key_exists($name, $this->foreignKeys);
    }

    /**
     * Determine whether the table has an index.
     *
     * @param string $name The index name.
     * @return bool TRUE if the table has the index, otherwise FALSE.
     */
    public function hasIndex(string $name): bool
    {
        return array_key_exists($name, $this->indexes);
    }

    /**
     * Get a table index.
     *
     * @param string $name The index name.
     * @return Index The Index
     */
    public function index(string $name): Index
    {
        if (!array_key_exists($name, $this->indexes)) {
            throw ForgeException::forInvalidIndex($this->name, $name);
        }

        return $this->indexes[$name];
    }

    /**
     * Get all table indexes.
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
     * @return Table The Table.
     */
    public function rename(string $table): static
    {
        if ($this->schemaTable) {
            $this->newName = $table;
        } else {
            $this->name = $table;
        }

        return $this;
    }

    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return Table The Table.
     */
    abstract public function setPrimaryKey(array|string $columns): static;

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     */
    abstract public function sql(): array;

    /**
     * Get the table data as an array.
     *
     * @return array The table data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'comment' => $this->comment,
        ];
    }

    /**
     * Build a Column.
     *
     * @param string $name The column name.
     * @param array $data The column data.
     * @return Column The Column.
     */
    abstract protected function buildColumn(string $name, array $data): Column;

    /**
     * Build a ForeignKey.
     *
     * @param string $name The foreign key name.
     * @param array $data The foreign key data.
     * @return ForeignKey The ForeignKey.
     */
    protected function buildForeignKey(string $name, array $data): ForeignKey
    {
        return $this->container->build(ForeignKey::class, [
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
     * @return Index The Index.
     */
    protected function buildIndex(string $name, array $data): Index
    {
        return $this->container->build(Index::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Reload table data from the schema.
     *
     * @param bool $forceReset Whether to forcefully reload the schema data.
     */
    protected function reloadSchema(bool $forceReset = false): void
    {
        $this->schemaTable = $this->schema->table($this->name);

        if ($forceReset) {
            $this->comment = $this->schemaTable->getComment();
        } else {
            $this->comment ??= $this->schemaTable->getComment();
        }

        $this->columns = $this->schemaTable->columns()
            ->map(fn(SchemaColumn $column): Column => $this->buildColumn(
                $column->getName(),
                array_diff_key($column->toArray(), ['name' => true])
            ))
            ->toArray();

        $this->indexes = $this->schemaTable->indexes()
            ->map(fn(SchemaIndex $index): Index => $this->buildIndex(
                $index->getName(),
                array_diff_key($index->toArray(), ['name' => true])
            ))
            ->toArray();

        $this->foreignKeys = $this->schemaTable->foreignKeys()
            ->map(fn(SchemaForeignKey $foreignKey): ForeignKey => $this->buildForeignKey(
                $foreignKey->getName(),
                array_diff_key($foreignKey->toArray(), ['name' => true])
            ))
            ->toArray();
    }

    /**
     * Change the order of a column.
     *
     * @param string $name The column name.
     * @param bool $first Whether the column should be moved to the start.
     * @param string|null $after The column to move the new column after.
     */
    protected function updateColumnOrder(string $name, bool $first = false, string|null $after = null): void
    {
        if (!$first && !$after) {
            return;
        }

        $column = $this->columns[$name];
        unset($this->columns[$name]);

        if ($first) {
            $beforeColumns = [];
            $afterColumns = $this->columns;
        } else {
            $afterIndex = array_search($after, array_keys($this->columns));

            $beforeColumns = array_slice($this->columns, 0, $afterIndex + 1);
            $afterColumns = array_slice($this->columns, $afterIndex + 1);
        }

        $this->columns = array_merge($beforeColumns, [$name => $column], $afterColumns);
    }
}
