<?php
declare(strict_types=1);

namespace Tests\Sqlite;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Sqlite\SqliteConnection;
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
        ConnectionManager::clear();
        ConnectionManager::setConfig([
            'default' => [
                'className' => SqliteConnection::class,
                'persist' => true,
            ],
        ]);

        $this->db = ConnectionManager::use();
        $this->schema = SchemaRegistry::getSchema($this->db);
        $this->forge = ForgeRegistry::getForge($this->db);
        $this->generator = $this->forge->generator();
    }

    protected function tearDown(): void
    {
        $this->db->query('DROP TABLE IF EXISTS test');
        $this->db->query('DROP TABLE IF EXISTS test_values');
        $this->db->query('DROP TABLE IF EXISTS other');
    }
}
