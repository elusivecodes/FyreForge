<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\Utility\Traits\MacroTrait;

use function get_object_vars;

/**
 * Forge
 */
abstract class Forge
{
    use MacroTrait;

    protected QueryGenerator $generator;

    /**
     * New Forge constructor.
     *
     * @param Container $container The Container.
     * @param Connection The Connection.
     */
    public function __construct(
        protected Container $container,
        protected Connection $connection
    ) {}

    /**
     * Get the debug info of the object.
     *
     * @return array The debug info.
     */
    public function __debugInfo(): array
    {
        $data = get_object_vars($this);

        unset($data['container']);
        unset($data['connection']);
        unset($data['generator']);

        return $data;
    }

    /**
     * Add a column to a table.
     *
     * @param string $tableName The table name.
     * @param string $columnName The column name.
     * @param array $options The column options.
     * @return Forge The Forge.
     */
    public function addColumn(string $tableName, string $columnName, array $options = []): static
    {
        $this->build($tableName)
            ->addColumn($columnName, $options)
            ->execute();

        return $this;
    }

    /**
     * Add a foreign key to a table.
     *
     * @param string $tableName The table name.
     * @param string $foreignKeyName The foreign key name.
     * @param array $options The foreign key options.
     * @return Forge The Forge.
     */
    public function addForeignKey(string $tableName, string $foreignKeyName, array $options = []): static
    {
        $this->build($tableName)
            ->addForeignKey($foreignKeyName, $options)
            ->execute();

        return $this;
    }

    /**
     * Add an index to a table.
     *
     * @param string $tableName The table name.
     * @param string $indexName The index name.
     * @param array $options The index options.
     * @return Forge The Forge.
     */
    public function addIndex(string $tableName, string $indexName, array $options = []): static
    {
        $this->build($tableName)
            ->addIndex($indexName, $options)
            ->execute();

        return $this;
    }

    /**
     * Alter a table.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return Forge The Forge.
     */
    public function alterTable(string $tableName, array $options = []): static
    {
        $this->build($tableName, $options)
            ->execute();

        return $this;
    }

    /**
     * Build a table schema.
     *
     * @param string $name The table name.
     * @param array $options The table options.
     * @return Table The Table.
     */
    abstract public function build(string $name, array $options = []): Table;

    /**
     * Change a table column.
     *
     * @param string $tableName The table name.
     * @param string $columnName The column name.
     * @param array $options The column options.
     * @return Forge The Forge.
     */
    public function changeColumn(string $tableName, string $columnName, array $options): static
    {
        $this->build($tableName)
            ->changeColumn($columnName, $options)
            ->execute();

        return $this;
    }

    /**
     * Create a new table.
     *
     * @param string $tableName The table name.
     * @param array $columns The table columns.
     * @param array $indexes The table indexes.
     * @param array $foreignKeys The table foreign keys.
     * @param array $options The table options.
     * @return Forge The Forge.
     */
    public function createTable(string $tableName, array $columns, array $indexes = [], array $foreignKeys = [], array $options = []): static
    {
        $table = $this->build($tableName, $options);

        foreach ($columns as $columnName => $options) {
            $table->addColumn($columnName, $options);
        }

        foreach ($indexes as $indexName => $options) {
            $table->addIndex($indexName, $options);
        }

        foreach ($foreignKeys as $foreignKeyName => $options) {
            $table->addForeignKey($foreignKeyName, $options);
        }

        $table->execute();

        return $this;
    }

    /**
     * Drop a column from a table.
     *
     * @param string $tableName The table name.
     * @param string $columnName The column name.
     * @return Forge The Forge.
     */
    public function dropColumn(string $tableName, string $columnName): static
    {
        $this->build($tableName)
            ->dropColumn($columnName)
            ->execute();

        return $this;
    }

    /**
     * Drop a foreign key from a table.
     *
     * @param string $tableName The table name.
     * @param string $foreignKeyName The foreign key name.
     * @return MysqlForge The Forge.
     */
    public function dropForeignKey(string $tableName, string $foreignKeyName): static
    {
        $this->build($tableName)
            ->dropForeignKey($foreignKeyName)
            ->execute();

        return $this;
    }

    /**
     * Drop an index from a table.
     *
     * @param string $tableName The table name.
     * @param string $indexName The index name.
     * @return Forge The Forge.
     */
    public function dropIndex(string $tableName, string $indexName): static
    {
        $this->build($tableName)
            ->dropIndex($indexName)
            ->execute();

        return $this;
    }

    /**
     * Drop a table.
     *
     * @param string $table The table name.
     * @return Forge The Forge.
     */
    public function dropTable(string $tableName): static
    {
        $this->build($tableName)
            ->drop()
            ->execute();

        return $this;
    }

    /**
     * Get the forge query generator.
     *
     * @return QueryGenerator The query generator.
     */
    abstract public function generator(): QueryGenerator;

    /**
     * Get the Connection.
     *
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Rename a column.
     *
     * @param string $tableName The table name.
     * @param string $columnName The old column name.
     * @param string $newColumnName The new column name.
     * @return Forge The Forge.
     */
    public function renameColumn(string $tableName, string $columnName, string $newColumnName): static
    {
        $this->build($tableName)
            ->changeColumn($columnName, ['name' => $newColumnName])
            ->execute();

        return $this;
    }

    /**
     * Rename a table.
     *
     * @param string $newTableName The new table name.
     * @param string $table The old table name.
     * @return Forge The Forge.
     */
    public function renameTable(string $tableName, string $newTableName): static
    {
        $this->build($tableName)
            ->rename($newTableName)
            ->execute();

        return $this;
    }
}
