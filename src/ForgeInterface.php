<?php
declare(strict_types=1);

namespace Fyre\Forge;

use
    Fyre\Schema\SchemaInterface;

/**
 * ForgeInterface
 */
interface ForgeInterface extends SchemaInterface
{

    /**
     * Add a column to a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function addColumn(string $table, string $column, array $options = []): bool;

    /**
     * Generate SQL for adding a column to a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function addColumnSql(string $table, string $column, array $options = []): string;

    /**
     * Add a foreign key to a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return bool TRUE if the query was successful.
     */
    public function addForeignKey(string $table, string $foreignKey, array $options = []): bool;

    /**
     * Generate SQL for adding a foreign key to a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    public function addForeignKeySql(string $table, string $foreignKey, array $options = []): string;

    /**
     * Add an index to a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return bool TRUE if the query was successful.
     */
    public function addIndex(string $table, string $index, array $options = []): bool;

    /**
     * Generate SQL for adding an index to a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     */
    public function addIndexSql(string $table, string $index, array $options = []): string;

    /**
     * Alter a table.
     * @param string $table The table name.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function alterTable(string $table, array $options = []): bool;

    /**
     * Generate SQL for altering a table.
     * @param string $table The table name.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    public function alterTableSql(string $table, array $options = []): string;

    /**
     * Build a table forge.
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return TableForgeInterface The TableForge.
     */
    public function build(string $tableName, array $options = []): TableForgeInterface;

    /**
     * Change a table column.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function changeColumn(string $table, string $column, array $options): bool;

    /**
     * Generate SQL for changing a table column.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function changeColumnSql(string $table, string $column, array $options): string;

    /**
     * Create a new schema.
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return bool TRUE if the query was successful.
     */
    public function createSchema(string $schema, array $options = []): bool;

    /**
     * Generate SQL for creating a new schema.
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return string The SQL query.
     */
    public function createSchemaSql(string $schema, array $options = []): string;

    /**
     * Create a new table.
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function createTable(string $table, array $columns, array $options = []): bool;

    /**
     * Generate SQL for creating a new table.
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    public function createTableSql(string $table, array $columns, array $options = []): string;

    /**
     * Drop a column from a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return bool TRUE if the query was successful.
     */
    public function dropColumn(string $table, string $column, array $options = []): bool;

    /**
     * Generate SQL for dropping a column from a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function dropColumnSql(string $table, string $column, array $options = []): string;

    /**
     * Drop a foreign key from a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return bool TRUE if the query was successful.
     */
    public function dropForeignKey(string $table, string $foreignKey): bool;

    /**
     * Generate SQL for dropping a foreign key from a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return string The SQL query.
     */
    public function dropForeignKeySql(string $table, string $foreignKey): string;

    /**
     * Drop an index from a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @return bool TRUE if the query was successful.
     */
    public function dropIndex(string $table, string $index): bool;

    /**
     * Generate SQL for dropping an index from a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @return string The SQL query.
     */
    public function dropIndexSql(string $table, string $index): string;

    /**
     * Drop a schema.
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return bool TRUE if the query was successful.
     */
    public function dropSchema(string $schema, array $options = []): bool;

    /**
     * Generate SQL for dropping a schema.
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return string The SQL query.
     */
    public function dropSchemaSql(string $schema, array $options = []): string;

    /**
     * Drop a table.
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return bool TRUE if the query was successful.
     */
    public function dropTable(string $table, array $options = []): bool;

    /**
     * Generate SQL for dropping a table.
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function dropTableSql(string $table, array $options = []): string;

    /**
     * Parse column options.
     * @param array $options The column options.
     * @return array The parsed options.
     */
    public function parseColumnOptions(array $options = []): array;

    /**
     * Parse foreign key options.
     * @param array $options The foreign key options.
     * @return array The parsed options.
     */
    public function parseForeignKeyOptions(array $options = []): array;

    /**
     * Parse index options.
     * @param array $options The index options.
     * @return array The parsed options.
     */
    public function parseIndexOptions(array $options = []): array;

    /**
     * Parse table options.
     * @param array $options The table options.
     * @return array The parsed options.
     */
    public function parseTableOptions(array $options = []): array;

    /**
     * Rename a table.
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return bool TRUE if the query was successful.
     */
    public function renameTable(string $table, string $newTable): bool;

    /**
     * Generate SQL for renaming a table.
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return string The SQL query.
     */
    public function renameTableSql(string $table, string $newTable): string;

}
