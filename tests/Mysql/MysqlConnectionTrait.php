<?php
declare(strict_types=1);

namespace Tests\Mysql;

use Fyre\DB\Connection;
use Fyre\DB\ConnectionManager;
use Fyre\DB\Handlers\Mysql\MysqlConnection;
use Fyre\Forge\Forge;
use Fyre\Forge\ForgeQueryGenerator;
use Fyre\Forge\ForgeRegistry;
use Fyre\Schema\Schema;
use Fyre\Schema\SchemaRegistry;

use function getenv;

trait MysqlConnectionTrait
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
                'className' => MysqlConnection::class,
                'host' => getenv('MYSQL_HOST'),
                'username' => getenv('MYSQL_USERNAME'),
                'password' => getenv('MYSQL_PASSWORD'),
                'database' => getenv('MYSQL_DATABASE'),
                'port' => getenv('MYSQL_PORT'),
                'collation' => 'utf8mb4_unicode_ci',
                'charset' => 'utf8mb4',
                'compress' => true,
                'persist' => true,
            ],
        ]);

        $this->db = ConnectionManager::use();
        $this->schema = SchemaRegistry::getSchema($this->db);
        $this->forge = ForgeRegistry::getForge($this->db);
        $this->generator = $this->forge->generator();

        $this->db->query('DROP TABLE IF EXISTS test');
        $this->db->query('DROP TABLE IF EXISTS test_values');
        $this->db->query('DROP TABLE IF EXISTS other');
        $this->db->query('DROP SCHEMA IF EXISTS other');
    }
}
