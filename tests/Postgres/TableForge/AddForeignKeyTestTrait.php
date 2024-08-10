<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddForeignKeyTestTrait
{
    public function testAddForeignKeyExistingForeignKey(): void
    {
        $this->expectException(ForgeException::class);

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
        ], [
            'foreignKeys' => [
                'value_id' => [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ],
            ],
        ]);

        $this->forge
            ->build('test')
            ->addForeignKey('value_id', [
                'referencedTable' => 'test_values',
                'referencedColumns' => 'id',
            ]);
    }

    public function testAddForeignKeySqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value_id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD CONSTRAINT value_id FOREIGN KEY (value_id) REFERENCES test_values (id)',
            ],
            $this->forge
                ->build('test')
                ->addForeignKey('value_id', [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ])
                ->sql()
        );
    }

    public function testAddForeignKeySqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INTEGER NOT NULL, value_id INTEGER NOT NULL, CONSTRAINT value_id FOREIGN KEY (value_id) REFERENCES test_values (id))',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value_id', [
                    'type' => 'integer',
                ])
                ->addForeignKey('value_id', [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ])
                ->sql()
        );
    }
}
