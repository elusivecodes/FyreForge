<?php
declare(strict_types=1);

namespace Tests\Forge;

trait DropForeignKeyTestTrait
{

    public function testDropForeignKey(): void
    {
        $this->forge->createTable('test_values', [
            'id' => [
                'type' => 'int'
            ]
        ], [
            'indexes' => [
                'PRIMARY' => [
                    'columns' => [
                        'id'
                    ]
                ]
            ]
        ]);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value_id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->addForeignKey('test', 'value_id', [
            'referencedTable' => 'test_values',
            'referencedColumns' => 'id'
        ]);

        $this->forge->dropForeignKey('test', 'value_id');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasForeignKey('value_id')
        );
    }

    public function testDropForeignKeySql(): void
    {
        $this->assertSame(
            'ALTER TABLE test DROP FOREIGN KEY value',
            $this->forge->dropForeignKeySql('test', 'value')
        );
    }

}
