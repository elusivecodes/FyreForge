<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Forge\Forge;
use Override;

/**
 * MysqlForge
 */
class MysqlForge extends Forge
{
    /**
     * Build a table schema.
     *
     * @param string $name The table name.
     * @param array $options The table options.
     * @return MysqlTable The Table.
     */
    #[Override]
    public function build(string $name, array $options = []): MysqlTable
    {
        return $this->container->build(MysqlTable::class, [
            'forge' => $this,
            'name' => $name,
            ...$options,
        ]);
    }

    /**
     * Create a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return MysqlForge The Forge.
     */
    public function createSchema(string $schema, array $options = []): static
    {
        $sql = $this->generator()->buildCreateSchema($schema, $options);

        $this->connection->query($sql);

        return $this;
    }

    /**
     * Drop a primary key from a table.
     *
     * @param string $tableName The table name.
     * @return MysqlForge The Forge.
     */
    public function dropPrimaryKey(string $tableName): static
    {
        return $this->dropIndex($tableName, 'PRIMARY');
    }

    /**
     * Drop a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return MysqlForge The Forge.
     */
    public function dropSchema(string $schema, array $options = []): static
    {
        $sql = $this->generator()->buildDropSchema($schema, $options);

        $this->connection->query($sql);

        return $this;
    }

    /**
     * Get the forge query generator.
     *
     * @return MysqlQueryGenerator The query generator.
     */
    #[Override]
    public function generator(): MysqlQueryGenerator
    {
        return $this->generator ??= $this->container->build(MysqlQueryGenerator::class, [
            'forge' => $this,
        ]);
    }
}
