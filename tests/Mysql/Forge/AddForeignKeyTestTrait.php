<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait AddForeignKeyTestTrait
{
    public function testAddForeignKey(): void
    {
        $this->forge->createTable('test_values', [
            'id' => [
                'type' => 'int',
            ],
        ], [
            'indexes' => [
                'PRIMARY' => [
                    'columns' => [
                        'id',
                    ],
                ],
            ],
        ]);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value_id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addForeignKey('test', 'value_id', [
            'referencedTable' => 'test_values',
            'referencedColumns' => 'id',
            'update' => 'CASCADE',
            'delete' => 'CASCADE',
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasForeignKey('value_id')
        );

        $this->assertSame(
            [
                'columns' => ['value_id'],
                'referencedTable' => 'test_values',
                'referencedColumns' => ['id'],
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ],
            $this->schema->describe('test')
                ->foreignKey('value_id')
        );
    }
}
