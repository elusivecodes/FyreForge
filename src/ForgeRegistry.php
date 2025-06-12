<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Container\Container;
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
class ForgeRegistry
{
    protected WeakMap $forges;

    protected array $handlers = [
        MysqlConnection::class => MysqlForge::class,
        PostgresConnection::class => PostgresForge::class,
        SqliteConnection::class => SqliteForge::class,
    ];

    /**
     * New SchemaRegistry constructor.
     *
     * @param Container $container The Container.
     */
    public function __construct(
        protected Container $container
    ) {
        $this->forges = new WeakMap();
    }

    /**
     * Map a Connection class to a Forge handler.
     *
     * @param string $connectionClass The Connection class.
     * @param string $forgeClass The Forge class.
     */
    public function map(string $connectionClass, string $forgeClass): void
    {
        $connectionClass = ltrim($connectionClass, '\\');

        $this->handlers[$connectionClass] = $forgeClass;
    }

    /**
     * Load a shared Forge for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Forge The Forge.
     */
    public function use(Connection $connection): Forge
    {
        return $this->forges[$connection] ??= $this->build($connection);
    }

    /**
     * Load a Forge for a Connection.
     *
     * @param Connection $connection The Connection.
     * @return Forge The Forge.
     *
     * @throws ForgeException if the handler is missing.
     */
    protected function build(Connection $connection): Forge
    {
        $connectionClass = get_class($connection);
        $connectionKey = $connectionClass;

        while (!array_key_exists($connectionKey, $this->handlers)) {
            $classParents ??= class_parents($connection);
            $connectionKey = array_shift($classParents);

            if (!$connectionKey) {
                throw ForgeException::forMissingHandler($connectionClass);
            }
        }

        $forgeClass = $this->handlers[$connectionKey];

        return $this->container->build($forgeClass, ['connection' => $connection]);
    }
}
