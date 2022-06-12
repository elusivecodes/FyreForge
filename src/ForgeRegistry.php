<?php
declare(strict_types=1);

namespace Fyre\Forge;

use
    Fyre\DB\Connection,
    Fyre\DB\Handlers\MySQL\MySQLConnection,
    Fyre\Forge\Handlers\MySQL\MySQLForge,
    Fyre\Schema\Schema,
    WeakMap;

use function
    array_key_exists,
    get_class,
    ltrim;

/**
 * ForgeRegistry
 */
abstract class ForgeRegistry
{

    protected static array $handlers = [
        MySQLConnection::class => MySQLForge::class
    ];

    protected static WeakMap $forges;

    /**
     * Get the Forge for a Connection.
     * @param Connection $connection The Connection.
     * @return ForgeInterface The Forge.
     */
    public static function getForge(Connection $connection): ForgeInterface
    {
        static::$forges ??= new WeakMap;

        return static::$forges[$connection] ??= static::loadForge($connection);
    }

    /**
     * Set a Forge handler for a Connection class.
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
     * @param Connection $connection The Connection.
     * @return ForgeInterface The Forge.
     */
    protected static function loadForge(Connection $connection): ForgeInterface
    {
        $connectionClass = get_class($connection);

        if (!array_key_exists($connectionClass, static::$handlers)) {
            throw ForgeException::forMissingHandler($connectionClass);
        }

        $forgeClass = static::$handlers[$connectionClass];

        return new $forgeClass($connection);
    }

}
