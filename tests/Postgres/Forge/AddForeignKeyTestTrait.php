<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait AddForeignKeyTestTrait
{
    public function testAddForeignKey(): void
    {
        $this->forge->createTable('test_values', [
            'id' => [
                'type' => 'integer',
            ],
        ], [
            'indexes' => [
                'test_values_pkey' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value_id' => [
                'type' => 'integer',
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
