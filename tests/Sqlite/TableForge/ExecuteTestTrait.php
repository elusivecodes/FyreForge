<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\DB\Exceptions\DbException;
use Fyre\Forge\Exceptions\ForgeException;

trait ExecuteTestTrait
{
    public function testExecuteAddColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->addColumn('value', [
                'type' => 'int',
                'nullable' => true,
                'default' => 'NULL',
            ])
            ->execute();

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }

    public function testExecuteAddForeignKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test_values', [
            'id' => [
                'type' => 'int',
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

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value_id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->addForeignKey('test_value_id', [
                'columns' => 'value_id',
                'referencedTable' => 'test_values',
                'referencedColumns' => 'id',
            ])
            ->execute();
    }

    public function testExecuteAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->addIndex('id')
            ->execute();

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }

    public function testExecuteChangeColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->changeColumn('value', [
                'name' => 'other',
            ])
            ->execute();

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('other')
        );
    }

    public function testExecuteCreateTable(): void
    {
        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->execute();

        $this->assertTrue(
            $this->schema->hasTable('test')
        );
    }

    public function testExecuteDrop(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->drop()
            ->execute();

        $this->assertFalse(
            $this->schema->hasTable('test')
        );
    }

    public function testExecuteDropColumn(): void
    {
        $this->expectException(DbException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropColumn('value')
            ->execute();

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }

    public function testExecuteDropForeignKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test_values', [
            'id' => [
                'type' => 'int',
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

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value_id' => [
                'type' => 'int',
            ],
        ], [
            'foreignKeys' => [
                'test_value_id' => [
                    'columns' => 'value_id',
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ],
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropForeignKey('test_value_id')
            ->execute();
    }

    public function testExecuteDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ], [
            'indexes' => [
                'id',
            ],
        ]);

        $this->forge
            ->build('test')
            ->dropIndex('id')
            ->execute();

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }

    public function testExecuteRename(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->rename('other')
            ->execute();

        $this->assertFalse(
            $this->schema->hasTable('test')
        );

        $this->assertTrue(
            $this->schema->hasTable('other')
        );
    }
}
