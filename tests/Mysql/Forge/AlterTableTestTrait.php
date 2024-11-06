<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

use Fyre\DB\Types\IntegerType;

trait AlterTableTestTrait
{
    public function testAlterTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->alterTable('test', [
            'engine' => 'MyISAM',
        ]);

        $this->assertSame(
            [
                'engine' => 'MyISAM',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
            ],
            $this->schema->table('test')
        );
    }
}
