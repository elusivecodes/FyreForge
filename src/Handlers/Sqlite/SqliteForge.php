<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

use Fyre\Forge\Forge;
use Override;

/**
 * SqliteForge
 */
class SqliteForge extends Forge
{
    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return SqliteTable The Table.
     */
    #[Override]
    public function build(string $tableName, array $options = []): SqliteTable
    {
        return $this->container->build(SqliteTable::class, [
            'forge' => $this,
            'name' => $tableName,
            ...$options,
        ]);
    }

    /**
     * Get the forge query generator.
     *
     * @return SqliteQueryGenerator The query generator.
     */
    #[Override]
    public function generator(): SqliteQueryGenerator
    {
        return $this->generator ??= $this->container->build(SqliteQueryGenerator::class, [
            'forge' => $this,
        ]);
    }
}
