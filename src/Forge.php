<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\DB\Connection;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;

/**
 * Forge
 */
abstract class Forge
{
    protected Connection $connection;

    protected ForgeQueryGenerator $generator;

    protected Schema $schema;

    /**
     * New Forge constructor.
     *
     * @param Connection The Connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->schema = SchemaRegistry::getSchema($connection);
    }

    /**
     * Add a column to a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function addColumn(string $table, string $column, array $options = []): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildAddColumn($column, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Add an index to a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return bool TRUE if the query was successful.
     */
    abstract public function addIndex(string $table, string $index, array $options = []): bool;

    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return TableForge The TableForge.
     */
    abstract public function build(string $tableName, array $options = []): TableForge;

    /**
     * Create a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function createTable(string $table, array $columns, array $options = []): bool
    {
        $sql = $this->generator()->buildCreateTable($table, $columns, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a column from a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return bool TRUE if the query was successful.
     */
    public function dropColumn(string $table, string $column, array $options = []): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildDropColumn($column, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop an index from a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @return bool TRUE if the query was successful.
     */
    public function dropIndex(string $table, string $index): bool
    {
        $sql = $this->generator()->buildDropIndex($index);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a table.
     *
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return bool TRUE if the query was successful.
     */
    public function dropTable(string $table, array $options = []): bool
    {
        $sql = $this->generator()->buildDropTable($table, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Get the forge query generator.
     *
     * @return ForgeQueryGenerator The query generator.
     */
    abstract public function generator(): ForgeQueryGenerator;

    /**
     * Get the Connection.
     *
     * @return Connection The Connection.
     */
    public function getConnection(): Connection
    {
        return $this->schema->getConnection();
    }

    /**
     * Rename a column.
     *
     * @param string $table The table name.
     * @param string $column The old column name.
     * @param string $newColumn The new column name.
     * @return bool TRUE if the query was successful.
     */
    public function renameColumn(string $table, string $column, string $newColumn): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildRenameColumn($column, $newColumn);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Rename a table.
     *
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return bool TRUE if the query was successful.
     */
    public function renameTable(string $table, string $newTable): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildRenameTable($newTable);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }
}
