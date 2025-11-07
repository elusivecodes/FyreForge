<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\Forge;
use Override;

/**
 * PostgresForge
 */
class PostgresForge extends Forge
{
    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return PostgresTable The Table.
     */
    #[Override]
    public function build(string $tableName, array $options = []): PostgresTable
    {
        return $this->container->build(PostgresTable::class, [
            'forge' => $this,
            'name' => $tableName,
            ...$options,
        ]);
    }

    /**
     * Create a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return PostgresForge The Forge.
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
     * @return PostgresForge The Forge.
     */
    public function dropPrimaryKey(string $tableName): static
    {
        return $this->dropIndex($tableName, $tableName.'_pkey');
    }

    /**
     * Drop a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return PostgresForge The Forge.
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
     * @return PostgresQueryGenerator The query generator.
     */
    #[Override]
    public function generator(): PostgresQueryGenerator
    {
        return $this->generator ??= $this->container->build(PostgresQueryGenerator::class, [
            'forge' => $this,
        ]);
    }
}
