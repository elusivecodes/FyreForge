<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DiffTestTrait
{
    public function testTableDiffChangeForeignKey(): void
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
            ->clear()
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->addColumn('value_id', [
                'type' => 'int',
            ])
            ->addForeignKey('test_value_id', [
                'columns' => 'value_id',
                'referencedTable' => 'test_values',
                'referencedColumns' => 'id',
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ])
            ->sql();
    }

    public function testTableDiffChangeIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ], [
            'indexes' => [
                'value',
            ],
        ]);

        $this->assertSame(
            [
                'DROP INDEX value',
                'CREATE UNIQUE INDEX value ON test (value)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                ])
                ->addIndex('value', [
                    'unique' => true,
                ])
                ->sql()
        );
    }

    public function testTableDiffSql(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
            'created' => [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'modified' => [
                'type' => 'datetime',
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'indexes' => [
                'primary' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
                'value' => [
                    'unique' => true,
                ],
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                    'autoIncrement' => true,
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255,
                ])
                ->addColumn('created', [
                    'type' => 'datetime',
                    'default' => 'CURRENT_TIMESTAMP',
                ])
                ->addColumn('modified', [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => 'NULL',
                ])
                ->setPrimaryKey('id')
                ->addIndex('value', [
                    'unique' => true,
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlAddColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value2' => [
                'type' => 'varchar',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value1 VARCHAR(80) NOT NULL',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addColumn('value1', [
                    'type' => 'varchar',
                ])
                ->addColumn('value2', [
                    'type' => 'varchar',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlAddForeignKey(): void
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
            ->clear()
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->addColumn('value_id', [
                'type' => 'int',
            ])
            ->addForeignKey('test_value_id', [
                'columns' => 'value_id',
                'referencedTable' => 'test_values',
                'referencedColumns' => 'id',
            ])
            ->sql();
    }

    public function testTableDiffSqlAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
        ]);

        $this->assertSame(
            [
                'CREATE INDEX value ON test (value)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255,
                ])
                ->addIndex('value')
                ->sql()
        );
    }

    public function testTableDiffSqlChangeColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge
            ->build('test')
            ->clear()
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->addColumn('value', [
                'type' => 'varchar',
                'length' => 255,
            ])
            ->sql();
    }

    public function testTableDiffSqlDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP COLUMN value',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropForeignKey(): void
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
                'value_id' => [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                ],
            ],
        ]);

        $this->forge
            ->build('test')
            ->clear()
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->addColumn('value_id', [
                'type' => 'int',
            ])
            ->sql();
    }

    public function testTableDiffSqlDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
        ], [
            'indexes' => [
                'value',
            ],
        ]);

        $this->assertSame(
            [
                'DROP INDEX value',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255,
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlPrimaryKey(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->clear()
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->setPrimaryKey('id')
            ->sql();
    }
}
