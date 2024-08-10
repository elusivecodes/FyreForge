<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

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
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'modified' => [
                'type' => 'datetime',
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'indexes' => [
                'primary' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->assertTrue(
            $this->schema->hasTable('test')
        );
    }
}
