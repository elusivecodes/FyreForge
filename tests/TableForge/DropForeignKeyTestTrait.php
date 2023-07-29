<?php
declare(strict_types=1);

namespace Tests\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropForeignKeyTestTrait
{

    public function testDropForeignKeySqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL, value_id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value_id', [
                    'type' => 'int'
                ])
                ->addForeignKey('value_id', [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id'
                ])
                ->dropForeignKey('value_id')
                ->sql()
        );
    }

    public function testDropForeignKeySqlExistingTable(): void
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
        ], [
            'foreignKeys' => [
                'value_id' => [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id'
                ]
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP FOREIGN KEY value_id'
            ],
            $this->forge
                ->build('test')
                ->dropForeignKey('value_id')
                ->sql()
        );
    }

    public function testDropForeignKeySqlExistingForeignKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge
            ->build('test')
            ->dropForeignKey('invalid');
    }

}
