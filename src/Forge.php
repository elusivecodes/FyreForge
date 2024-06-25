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
        $sql = $this->addColumnSql($table, $column, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for adding a column to a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    abstract public function addColumnSql(string $table, string $column, array $options = []): string;

    /**
     * Add a foreign key to a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return bool TRUE if the query was successful.
     */
    public function addForeignKey(string $table, string $foreignKey, array $options = []): bool
    {
        $sql = $this->addForeignKeySql($table, $foreignKey, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for adding a foreign key to a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    abstract public function addForeignKeySql(string $table, string $foreignKey, array $options = []): string;

    /**
     * Add an index to a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return bool TRUE if the query was successful.
     */
    public function addIndex(string $table, string $index, array $options = []): bool
    {
        $sql = $this->addIndexSql($table, $index, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for adding an index to a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     */
    abstract public function addIndexSql(string $table, string $index, array $options = []): string;

    /**
     * Alter a table.
     *
     * @param string $table The table name.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function alterTable(string $table, array $options = []): bool
    {
        $sql = $this->alterTableSql($table, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for altering a table.
     *
     * @param string $table The table name.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    abstract public function alterTableSql(string $table, array $options = []): string;

    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return TableForge The TableForge.
     */
    abstract public function build(string $tableName, array $options = []): TableForge;

    /**
     * Change a table column.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function changeColumn(string $table, string $column, array $options): bool
    {
        $sql = $this->changeColumnSql($table, $column, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for changing a table column.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    abstract public function changeColumnSql(string $table, string $column, array $options): string;

    /**
     * Create a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return bool TRUE if the query was successful.
     */
    public function createSchema(string $schema, array $options = []): bool
    {
        $sql = $this->createSchemaSql($schema, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for creating a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return string The SQL query.
     */
    abstract public function createSchemaSql(string $schema, array $options = []): string;

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
        $sql = $this->createTableSql($table, $columns, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for creating a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    abstract public function createTableSql(string $table, array $columns, array $options = []): string;

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
        $sql = $this->dropColumnSql($table, $column, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for dropping a column from a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    abstract public function dropColumnSql(string $table, string $column, array $options = []): string;

    /**
     * Drop a foreign key from a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return bool TRUE if the query was successful.
     */
    public function dropForeignKey(string $table, string $foreignKey): bool
    {
        $sql = $this->dropForeignKeySql($table, $foreignKey);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for dropping a foreign key from a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return string The SQL query.
     */
    abstract public function dropForeignKeySql(string $table, string $foreignKey): string;

    /**
     * Drop an index from a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @return bool TRUE if the query was successful.
     */
    public function dropIndex(string $table, string $index): bool
    {
        $sql = $this->dropIndexSql($table, $index);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for dropping an index from a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @return string The SQL query.
     */
    abstract public function dropIndexSql(string $table, string $index): string;

    /**
     * Drop a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return bool TRUE if the query was successful.
     */
    public function dropSchema(string $schema, array $options = []): bool
    {
        $sql = $this->dropSchemaSql($schema, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for dropping a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return string The SQL query.
     */
    abstract public function dropSchemaSql(string $schema, array $options = []): string;

    /**
     * Drop a table.
     *
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return bool TRUE if the query was successful.
     */
    public function dropTable(string $table, array $options = []): bool
    {
        $sql = $this->dropTableSql($table, $options);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for dropping a table.
     *
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    abstract public function dropTableSql(string $table, array $options = []): string;

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
     * Merge queries.
     *
     * @param array $queries The queries.
     * @return array The queries.
     */
    public function mergeQueries(array $queries): array
    {
        return $queries;
    }

    /**
     * Parse column options.
     *
     * @param array $options The column options.
     * @return array The parsed options.
     */
    abstract public function parseColumnOptions(array $options = []): array;

    /**
     * Parse foreign key options.
     *
     * @param array $options The foreign key options.
     * @param string|null $foreignKey The foreign key name.
     * @return array The parsed options.
     */
    abstract public function parseForeignKeyOptions(array $options = [], string|null $foreignKey = null): array;

    /**
     * Parse index options.
     *
     * @param array $options The index options.
     * @param string|null $index The index name.
     * @return array The parsed options.
     */
    abstract public function parseIndexOptions(array $options = [], string|null $index = null): array;

    /**
     * Parse table options.
     *
     * @param array $options The table options.
     * @return array The parsed options.
     */
    abstract public function parseTableOptions(array $options = []): array;

    /**
     * Rename a table.
     *
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return bool TRUE if the query was successful.
     */
    public function renameTable(string $table, string $newTable): bool
    {
        $sql = $this->renameTableSql($table, $newTable);

        return $this->connection->query($sql);
    }

    /**
     * Generate SQL for renaming a table.
     *
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return string The SQL query.
     */
    abstract public function renameTableSql(string $table, string $newTable): string;
}
