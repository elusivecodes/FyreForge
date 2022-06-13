<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\DB\Connection,
    Fyre\DB\ConnectionManager,
    Fyre\DB\Handlers\MySQL\MySQLConnection,
    Fyre\Forge\ForgeInterface,
    Fyre\Forge\ForgeRegistry,
    Fyre\Schema\SchemaInterface,
    Fyre\Schema\SchemaRegistry;

use function
    getenv;

trait ConnectionTrait
{

    protected Connection $db;

    protected SchemaInterface $schema;

    protected ForgeInterface $forge;

    protected function setUp(): void
    {
        ConnectionManager::clear();
        ConnectionManager::setConfig('default', [
            'className' => MySQLConnection::class,
            'host' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_NAME'),
            'port' => getenv('DB_PORT'),
            'collation' => 'utf8mb4_unicode_ci',
            'charset' => 'utf8mb4',
            'compress' => true,
            'persist' => true
        ]);

        $this->db = ConnectionManager::use();
        $this->schema = SchemaRegistry::getSchema($this->db);
        $this->forge = ForgeRegistry::getForge($this->db);

        $this->db->query('DROP TABLE IF EXISTS test');
        $this->db->query('DROP TABLE IF EXISTS test_values');
        $this->db->query('DROP TABLE IF EXISTS other');
        $this->db->query('DROP SCHEMA IF EXISTS other');
    }

}
