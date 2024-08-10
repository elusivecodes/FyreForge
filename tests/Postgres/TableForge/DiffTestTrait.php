<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

trait DiffTestTrait
{
    public function testTableDiffChangeForeignKey(): void
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
                'ALTER TABLE test DROP CONSTRAINT value_id, ADD CONSTRAINT value_id FOREIGN KEY (value_id) REFERENCES test_values (id) ON UPDATE CASCADE ON DELETE CASCADE',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value_id', [
                    'type' => 'integer',
                ])
                ->addForeignKey('value_id', [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE',
                ])
                ->sql()
        );
    }

    public function testTableDiffChangeIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'character varying',
            ],
        ], [
            'indexes' => [
                'value',
            ],
        ]);

        $this->assertSame(
            [
                'DROP INDEX value',
                'ALTER TABLE test ADD CONSTRAINT value UNIQUE (value)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value', [
                    'type' => 'character varying',
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
                'type' => 'integer',
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => 'character varying',
                'length' => 255,
            ],
            'created' => [
                'type' => 'timestamp',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'modified' => [
                'type' => 'timestamp',
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'indexes' => [
                'test_pkey' => [
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
                    'type' => 'integer',
                    'autoIncrement' => true,
                ])
                ->addColumn('value', [
                    'type' => 'character varying',
                    'length' => 255,
                ])
                ->addColumn('created', [
                    'type' => 'timestamp',
                    'default' => 'CURRENT_TIMESTAMP',
                ])
                ->addColumn('modified', [
                    'type' => 'timestamp',
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
                'type' => 'integer',
            ],
            'value2' => [
                'type' => 'character varying',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value1 CHARACTER VARYING(80) NOT NULL',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value1', [
                    'type' => 'character varying',
                ])
                ->addColumn('value2', [
                    'type' => 'character varying',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlAddForeignKey(): void
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

        $this->assertSame(
            [
                'ALTER TABLE test ADD CONSTRAINT value_id FOREIGN KEY (value_id) REFERENCES test_values (id)',
            ],
            $this->forge
                ->build('test')
                ->clear()
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

    public function testTableDiffSqlAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'character varying',
                'length' => 255,
            ],
        ]);

        $this->assertSame(
            [
                'CREATE INDEX value ON test USING BTREE (value)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value', [
                    'type' => 'character varying',
                    'length' => 255,
                ])
                ->addIndex('value')
                ->sql()
        );
    }

    public function testTableDiffSqlAlterTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'COMMENT ON TABLE test IS \'This is the value\'',
            ],
            $this->forge
                ->build('test', [
                    'comment' => 'This is the value',
                ])
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlChangeColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'character varying',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ALTER COLUMN value TYPE CHARACTER VARYING(255)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value', [
                    'type' => 'character varying',
                    'length' => 255,
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'character varying',
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
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropForeignKey(): void
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
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value_id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'character varying',
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
                    'type' => 'integer',
                ])
                ->addColumn('value', [
                    'type' => 'character varying',
                    'length' => 255,
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlPrimaryKey(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD CONSTRAINT PRIMARY KEY (id)',
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->setPrimaryKey('id')
                ->sql()
        );
    }
}
