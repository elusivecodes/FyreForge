<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait AlterTableTestTrait
{
    public function testAlterTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
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
