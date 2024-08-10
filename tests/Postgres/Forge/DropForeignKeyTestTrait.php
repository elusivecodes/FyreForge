<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait DropForeignKeyTestTrait
{
    public function testDropForeignKey(): void
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
        ]);

        $this->forge->dropForeignKey('test', 'value_id');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasForeignKey('value_id')
        );
    }
}
