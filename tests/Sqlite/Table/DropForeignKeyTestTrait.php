<?php
declare(strict_types=1);

namespace Tests\Sqlite\Table;

use Fyre\DB\Types\IntegerType;
use Fyre\Forge\Exceptions\ForgeException;

trait DropForeignKeyTestTrait
{
    public function testDropForeignKeyExistingForeignKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropForeignKey('invalid');
    }

    public function testDropForeignKeySqlExistingTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test_values', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ], [
            'primary' => [
                'columns' => [
                    'id',
                ],
                'primary' => true,
            ],
        ]);

        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value_id' => [
                'type' => IntegerType::class,
            ],
        ], foreignKeys: [
            'test_value_id' => [
                'columns' => 'value_id',
                'referencedTable' => 'test_values',
                'referencedColumns' => 'id',
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropForeignKey('value_id')
            ->sql();
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
                    'type' => IntegerType::class,
                ])
                ->addColumn('value_id', [
                    'type' => IntegerType::class,
                ])
                ->addForeignKey('test_value_id', [
                    'columns' => 'value_id',
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ])
                ->dropForeignKey('test_value_id')
                ->sql()
        );
    }
}
