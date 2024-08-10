<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait ChangeColumnTestTrait
{
    public function testChangeColumnAfter(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'varchar',
            ],
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'after' => 'id',
        ]);

        $this->assertSame(
            [
                'id',
                'value',
            ],
            $this->schema->describe('test')
                ->columnNames()
        );
    }

    public function testChangeColumnAutoIncrement(): void
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
                'PRIMARY' => [
                    'columns' => [
                        'id',
                    ],
                ],
            ],
        ]);

        $this->forge->changeColumn('test', 'id', [
            'type' => 'int',
            'autoIncrement' => true,
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 11,
                'precision' => 0,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => true,
            ],
            $this->schema->describe('test')
                ->column('id')
        );
    }

    public function testChangeColumnCharsetCollation(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'varchar',
            'charset' => 'utf8mb3',
            'collation' => 'utf8mb3_unicode_ci',
        ]);

        $this->assertSame(
            [
                'type' => 'varchar',
                'length' => 80,
                'precision' => null,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => 'utf8mb3',
                'collation' => 'utf8mb3_unicode_ci',
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnComment(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'varchar',
            'comment' => 'This is the value',
        ]);

        $this->assertSame(
            [
                'type' => 'varchar',
                'length' => 80,
                'precision' => null,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => 'This is the value',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnDefault(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'int',
            'default' => '1',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 11,
                'precision' => 0,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => '1',
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnFirst(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'varchar',
            ],
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->changeColumn('test', 'id', [
            'first' => true,
        ]);

        $this->assertSame(
            [
                'id',
                'value',
            ],
            $this->schema->describe('test')
                ->columnNames()
        );
    }

    public function testChangeColumnLength(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'varchar',
            ],
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'int',
            'length' => 9,
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 9,
                'precision' => 0,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnLengthVarchar(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'varchar',
            ],
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'varchar',
            'length' => 255,
        ]);

        $this->assertSame(
            [
                'type' => 'varchar',
                'length' => 255,
                'precision' => null,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnNullable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'int',
            'nullable' => true,
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 11,
                'precision' => 0,
                'values' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnPrecision(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'decimal',
            'precision' => 2,
        ]);

        $this->assertSame(
            [
                'type' => 'decimal',
                'length' => 11,
                'precision' => 2,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnRename(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'name' => 'other',
            'type' => 'int',
        ]);

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('other')
        );
    }

    public function testChangeColumnType(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'int',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 11,
                'precision' => 0,
                'values' => null,
                'nullable' => false,
                'unsigned' => false,
                'default' => null,
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testChangeColumnUnsigned(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => 'int',
            'unsigned' => true,
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 10,
                'precision' => 0,
                'values' => null,
                'nullable' => false,
                'unsigned' => true,
                'default' => null,
                'charset' => null,
                'collation' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }
}
