<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropForeignKeyTestTrait
{
    public function testDropForeignKeyExistingForeignKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropForeignKey('invalid');
    }

    public function testDropForeignKeySqlExistingTable(): void
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
        ], [
            'foreignKeys' => [
                'value_id' => [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ],
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP CONSTRAINT value_id',
            ],
            $this->forge
                ->build('test')
                ->dropForeignKey('value_id')
                ->sql()
        );
    }

    public function testDropForeignKeySqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INTEGER NOT NULL, value_id INTEGER NOT NULL)',
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
                ->dropForeignKey('value_id')
                ->sql()
        );
    }
}
