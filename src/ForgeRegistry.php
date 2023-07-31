<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\DB\Connection;
use Fyre\DB\Handlers\MySQL\MySQLConnection;
use Fyre\Forge\Handlers\MySQL\MySQLForge;
use WeakMap;

use function array_key_exists;
use function get_class;
use function ltrim;

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
     * @return Forge The Forge.
     */
    public static function getForge(Connection $connection): Forge
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
     * @return Forge The Forge.
     */
    protected static function loadForge(Connection $connection): Forge
    {
        $connectionClass = get_class($connection);

        if (!array_key_exists($connectionClass, static::$handlers)) {
            throw ForgeException::forMissingHandler($connectionClass);
        }

        $forgeClass = static::$handlers[$connectionClass];

        return new $forgeClass($connection);
    }

}
