<?php
declare(strict_types=1);

namespace Tests\TableForge;

trait DiffTestTrait
{

    public function testTableDiffSql(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
                'extra' => 'AUTO_INCREMENT'
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'created' => [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP()'
            ],
            'modified' => [
                'type' => 'datetime',
                'nullable' => true,
                'default' => 'NULL',
                'extra' => 'ON UPDATE CURRENT_TIMESTAMP()'
            ]
        ], [
            'indexes' => [
                'PRIMARY' => [
                    'columns' => [
                        'id'
                    ]
                ],
                'value' => [
                    'unique' => true
                ]
            ]
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int',
                    'extra' => 'AUTO_INCREMENT'
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255
                ])
                ->addColumn('created', [
                    'type' => 'datetime',
                    'default' => 'CURRENT_TIMESTAMP()'
                ])
                ->addColumn('modified', [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => 'NULL',
                    'extra' => 'ON UPDATE CURRENT_TIMESTAMP()'
                ])
                ->setPrimaryKey('id')
                ->addIndex('value', [
                    'unique' => true
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlAddColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value2' => [
                'type' => 'varchar'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value1 VARCHAR(80) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL AFTER id'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value1', [
                    'type' => 'varchar'
                ])
                ->addColumn('value2', [
                    'type' => 'varchar'
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlAddForeignKey(): void
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

        $this->assertSame(
            [
                'ALTER TABLE test ADD FOREIGN KEY value_id (value_id) REFERENCES test_values (id)'
            ],
            $this->forge
                ->build('test')
                ->clear()
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
                ->sql()
        );
    }

    public function testTableDiffSqlAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD INDEX value (value) USING BTREE'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255
                ])
                ->addIndex('value')
                ->sql()
        );
    }

    public function testTableDiffSqlAlterTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ENGINE = MyISAM'
            ],
            $this->forge
                ->build('test', [
                    'engine' => 'MyISAM'
                ])
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlChangeColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'varchar'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test CHANGE COLUMN value value VARCHAR(255) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlChangeColumnOrder(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value1' => [
                'type' => 'varchar'
            ],
            'value2' => [
                'type' => 'varchar'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test CHANGE COLUMN value2 value2 VARCHAR(80) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL AFTER id'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value2', [
                    'type' => 'varchar'
                ])
                ->addColumn('value1', [
                    'type' => 'varchar'
                ])
                ->sql()
        );
    }

    public function testTableDiffChangeIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'varchar'
            ]
        ], [
            'indexes' => [
                'value'
            ]
        ]);

        $this->assertSame(
            [
                'DROP INDEX value ON test',
                'ALTER TABLE test ADD UNIQUE KEY value (value) USING BTREE'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value', [
                    'type' => 'varchar'
                ])
                ->addIndex('value', [
                    'unique' => true
                ])
                ->sql()
        );
    }

    public function testTableDiffChangeForeignKey(): void
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
                'ALTER TABLE test DROP FOREIGN KEY value_id',
                'ALTER TABLE test ADD FOREIGN KEY value_id (value_id) REFERENCES test_values (id) ON UPDATE CASCADE ON DELETE CASCADE'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value_id', [
                    'type' => 'int'
                ])
                ->addForeignKey('value_id', [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => 'id',
                    'update' => 'CASCADE',
                    'delete' => 'CASCADE'
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP COLUMN value'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropForeignKey(): void
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
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value_id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255
            ]
        ], [
            'indexes' => [
                'value'
            ]
        ]);

        $this->assertSame(
            [
                'DROP INDEX value ON test'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value', [
                    'type' => 'varchar',
                    'length' => 255
                ])
                ->sql()
        );
    }

    public function testTableDiffSqlPrimaryKey(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD PRIMARY KEY (id)'
            ],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->setPrimaryKey('id')
                ->sql()
        );
    }

}
