<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait ChangeColumnTestTrait
{
    public function testChangeColumnAfter(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => StringType::class,
            ],
            'id' => [
                'type' => IntegerType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
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
            'type' => IntegerType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => StringType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => StringType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => IntegerType::class,
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
                'type' => StringType::class,
            ],
            'id' => [
                'type' => IntegerType::class,
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
                'type' => StringType::class,
            ],
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => IntegerType::class,
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
                'type' => StringType::class,
            ],
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => StringType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => IntegerType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => DecimalType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'name' => 'other',
            'type' => IntegerType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => IntegerType::class,
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
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->changeColumn('test', 'value', [
            'type' => IntegerType::class,
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
