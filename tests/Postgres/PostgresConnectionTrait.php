<?php
declare(strict_types=1);

namespace Tests\Postgres;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Postgres\PostgresConnection;
use Fyre\Forge\Forge;
use Fyre\Forge\ForgeQueryGenerator;
use Fyre\Forge\ForgeRegistry;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;

use function getenv;

trait PostgresConnectionTrait
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
                'className' => PostgresConnection::class,
                'host' => getenv('POSTGRES_HOST'),
                'username' => getenv('POSTGRES_USERNAME'),
                'password' => getenv('POSTGRES_PASSWORD'),
                'database' => getenv('POSTGRES_DATABASE'),
                'port' => getenv('POSTGRES_PORT'),
                'charset' => 'utf8',
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
        $this->db->query('DROP SCHEMA IF EXISTS other');
    }
}
