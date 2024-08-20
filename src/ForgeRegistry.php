<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\DB\Connection;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\DB\Handlers\Postgres\PostgresConnection;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\Handlers\Mysql\MysqlForge;
use Fyre\Forge\Handlers\Postgres\PostgresForge;
use Fyre\Forge\Handlers\Sqlite\SqliteForge;
use WeakMap;

use function array_key_exists;
use function array_shift;
use function class_parents;
use function get_class;
use function ltrim;

/**
 * ForgeRegistry
 */
abstract class ForgeRegistry
{
    protected static WeakMap $forges;

    protected static array $handlers = [
        MysqlConnection::class => MysqlForge::class,
        PostgresConnection::class => PostgresForge::class,
        SqliteConnection::class => SqliteForge::class,
    ];

    /**
     * Get the Forge for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Forge The Forge.
     */
    public static function getForge(Connection $connection): Forge
    {
        static::$forges ??= new WeakMap();

        return static::$forges[$connection] ??= static::loadForge($connection);
    }

    /**
     * Set a Forge handler for a Connection class.
     *
     * @param string $connectionClass The Connection class.
     * @param string $forgeClass The Forge class.
     */
    public static function setHandler(string $connectionClass, string $forgeClass): void
    {
        $connectionClass = ltrim($connectionClass, '\\');

        static::$handlers[$connectionClass] = $forgeClass;
    }

    /**
     * Load a Forge for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Forge The Forge.
     *
     * @throws ForgeException if the handler is missing.
     */
    protected static function loadForge(Connection $connection): Forge
    {
        $connectionClass = get_class($connection);
        $connectionKey = $connectionClass;

        while (!array_key_exists($connectionKey, static::$handlers)) {
            $classParents ??= class_parents($connection);
            $connectionKey = array_shift($classParents);

            if (!$connectionKey) {
                throw ForgeException::forMissingHandler($connectionClass);
            }
        }

        $forgeClass = static::$handlers[$connectionClass];

        return new $forgeClass($connection);
    }
}
