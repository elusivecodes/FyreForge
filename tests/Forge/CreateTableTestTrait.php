<?php
declare(strict_types=1);

namespace Tests\Forge;

trait CreateTableTestTrait
{
    public function testCreateTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
                'extra' => 'AUTO_INCREMENT',
            ],
            'value' => [
                'type' => 'varchar',
                'length' => 255,
            ],
            'created' => [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP()',
            ],
            'modified' => [
                'type' => 'datetime',
                'nullable' => true,
                'default' => 'NULL',
                'extra' => 'ON UPDATE CURRENT_TIMESTAMP()',
            ],
        ], [
            'indexes' => [
                'PRIMARY' => [
                    'columns' => [
                        'id',
                    ],
                ],
                'value' => [
                    'unique' => true,
                ],
            ],
        ]);

        $this->assertTrue(
            $this->schema->hasTable('test')
        );
    }

    public function testCreateTableSql(): void
    {
        $this->assertSame(
            'CREATE TABLE test (id INT(11) NOT NULL AUTO_INCREMENT, value INT(11) NOT NULL, created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, modified DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY value (value) USING BTREE, CONSTRAINT value FOREIGN KEY (value) REFERENCES other (id)) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            $this->forge->createTableSql('test', [
                'id' => [
                    'type' => 'int',
                    'extra' => 'AUTO_INCREMENT',
                ],
                'value' => [
                    'type' => 'int',
                ],
                'created' => [
                    'type' => 'datetime',
                    'default' => 'CURRENT_TIMESTAMP',
                ],
                'modified' => [
                    'type' => 'datetime',
                    'nullable' => true,
                    'default' => 'NULL',
                    'extra' => 'ON UPDATE CURRENT_TIMESTAMP',
                ],
            ], [
                'indexes' => [
                    'PRIMARY' => [
                        'columns' => [
                            'id',
                        ],
                    ],
                    'value' => [
                        'unique' => true,
                    ],
                ],
                'foreignKeys' => [
                    'value' => [
                        'referencedTable' => 'other',
                        'referencedColumns' => 'id',
                    ],
                ],
            ])
        );
    }

    public function testCreateTableSqlCharsetCollation(): void
    {
        $this->assertSame(
            'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8\' COLLATE = \'utf8_unicode_ci\'',
            $this->forge->createTableSql('test', [
                'id' => [
                    'type' => 'int',
                ],
            ], [
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ])
        );
    }

    public function testCreateTableSqlEngine(): void
    {
        $this->assertSame(
            'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = MyISAM DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            $this->forge->createTableSql('test', [
                'id' => [
                    'type' => 'int',
                ],
            ], [
                'engine' => 'MyISAM',
            ])
        );
    }

    public function testCreateTableSqlIfNotExists(): void
    {
        $this->assertSame(
            'CREATE TABLE IF NOT EXISTS test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            $this->forge->createTableSql('test', [
                'id' => [
                    'type' => 'int',
                ],
            ], [
                'ifNotExists' => true,
            ])
        );
    }
}
