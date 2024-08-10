<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait CreateTableTestTrait
{
    public function testCreateTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
            'created' => [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP()',
            ],
            'modified' => [
                'type' => 'datetime',
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'engine' => 'MyISAM',
            'charset' => 'utf8mb3',
            'collation' => 'utf8mb3_unicode_ci',
            'indexes' => [
                'PRIMARY' => [
                    'columns' => [
                        'id',
                    ],
                ],
            ],
        ]);

        $this->assertTrue(
            $this->schema->hasTable('test')
        );

        $this->assertSame(
            [
                'engine' => 'MyISAM',
                'charset' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'comment' => '',
            ],
            $this->schema->table('test')
        );
    }
}
