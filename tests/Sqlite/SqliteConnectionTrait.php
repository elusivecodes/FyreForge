<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\Container\Container;
use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
use Fyre\DB\TypeParser;
use Fyre\Forge\Forge;
use Fyre\Forge\ForgeQueryGenerator;
use Fyre\Forge\ForgeRegistry;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;

trait SqliteConnectionTrait
{
    protected Connection $db;

    protected Forge $forge;

    protected ForgeQueryGenerator $generator;

    protected Schema $schema;

    protected function setUp(): void
    {
        $container = new Container();
        $container->singleton(TypeParser::class);
        $container->singleton(SchemaRegistry::class);

        $this->db = $container->use(ConnectionManager::class)->build([
            'className' => SqliteConnection::class,
        ]);

        $this->schema = $container->use(SchemaRegistry::class)->use($this->db);
        $this->forge = $container->use(ForgeRegistry::class)->use($this->db);
        $this->generator = $this->forge->generator();
    }

    protected function tearDown(): void
    {
        $this->db->query('DROP TABLE IF EXISTS test');
        $this->db->query('DROP TABLE IF EXISTS test_values');
        $this->db->query('DROP TABLE IF EXISTS other');
    }
}
