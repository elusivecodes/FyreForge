<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Forge\Forge;

/**
 * MysqlForge
 */
class MysqlForge extends Forge
{
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
        $generator = $this->generator();
        $alterSql = $generator->buildAddForeignKey($foreignKey, $options);
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
    public function addIndex(string $table, string $index, array $options = []): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildAddIndex($index, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Alter a table.
     *
     * @param string $table The table name.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function alterTable(string $table, array $options = []): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildTableOptions($options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return MysqlTableForge The MysqlTableForge.
     */
    public function build(string $tableName, array $options = []): MysqlTableForge
    {
        return $this->container->build(MysqlTableForge::class, [
            'forge' => $this,
            'tableName' => $tableName,
            'options' => $options,
        ]);
    }

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
        $generator = $this->generator();
        $alterSql = $generator->buildChangeColumn($column, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Create a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return bool TRUE if the query was successful.
     */
    public function createSchema(string $schema, array $options = []): bool
    {
        $sql = $this->generator()->buildCreateSchema($schema, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a foreign key from a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return bool TRUE if the query was successful.
     */
    public function dropForeignKey(string $table, string $foreignKey): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildDropForeignKey($foreignKey);
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
        $generator = $this->generator();
        $alterSql = $generator->buildDropIndex($index);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a primary key from a table.
     *
     * @param string $table The table name.
     * @return bool TRUE if the query was successful.
     */
    public function dropPrimaryKey(string $table): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildDropPrimaryKey();
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return bool TRUE if the query was successful.
     */
    public function dropSchema(string $schema, array $options = []): bool
    {
        $sql = $this->generator()->buildDropSchema($schema, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Get the forge query generator.
     *
     * @return MysqlForgeQueryGenerator The query generator.
     */
    public function generator(): MysqlForgeQueryGenerator
    {
        return $this->generator ??= $this->container->build(MysqlForgeQueryGenerator::class, ['forge' => $this]);
    }
}
