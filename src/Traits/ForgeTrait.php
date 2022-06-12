<?php
declare(strict_types=1);

namespace Fyre\Forge\Traits;

/**
 * ForgeTrait
 */
trait ForgeTrait
{

    /**
     * Add a column to a table.
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
     * Add a foreign key to a table.
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
     * Add an index to a table.
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
     * Alter a table.
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
     * Change a table column.
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
     * Create a new schema.
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
     * Create a new table.
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
     * Drop a column from a table.
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
     * Drop a foreign key from a table.
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
     * Drop an index from a table.
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
     * Drop a schema.
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
     * Drop a table.
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
     * Rename a table.
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return bool TRUE if the query was successful.
     */
    public function renameTable(string $table, string $newTable): bool
    {
        $sql = $this->renameTableSql($table, $newTable);

        return $this->connection->query($sql);
    }

}
