<?php
declare(strict_types=1);

namespace Tests\Forge;

trait AddForeignKeyTestTrait
{

    public function testAddForeignKey(): void
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
            'referencedColumns' => 'id',
            'update' => 'cascade',
            'delete' => 'cascade'
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasForeignKey('value_id')
        );
    }

    public function testAddForeignKeySql(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD FOREIGN KEY value (value) REFERENCES other (id)',
            $this->forge->addForeignKeySql('test', 'value', [
                'referencedTable' => 'other',
                'referencedColumns' => 'id'
            ])
        );
    }

    public function testAddForeignKeySqlColumns(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD FOREIGN KEY test_value_other_id (value) REFERENCES other (id)',
            $this->forge->addForeignKeySql('test', 'test_value_other_id', [
                'columns' => 'value',
                'referencedTable' => 'other',
                'referencedColumns' => 'id'
            ])
        );
    }

    public function testAddForeignKeySqlMultipleColumns(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD FOREIGN KEY test_other (value, test) REFERENCES other (id, test)',
            $this->forge->addForeignKeySql('test', 'test_other', [
                'columns' => ['value', 'test'],
                'referencedTable' => 'other',
                'referencedColumns' => ['id', 'test']
            ])
        );
    }

    public function testAddForeignKeySqlUpdateDelete(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD FOREIGN KEY value (value) REFERENCES other (id) ON UPDATE CASCADE ON DELETE CASCADE',
            $this->forge->addForeignKeySql('test', 'value', [
                'referencedTable' => 'other',
                'referencedColumns' => 'id',
                'update' => 'cascade',
                'delete' => 'cascade'
            ])
        );
    }

}
