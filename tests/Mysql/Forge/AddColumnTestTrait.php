<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait AddColumnTestTrait
{
    public function testAddColumnAfter(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnCharsetCollation(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnComment(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnDefault(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnFirst(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'first' => true,
        ]);

        $this->assertSame(
            [
                'value',
                'id',
            ],
            $this->schema->describe('test')
                ->columnNames()
        );
    }

    public function testAddColumnLength(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnLengthVarchar(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnNullable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnPrecision(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnTypeBigInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'bigint',
        ]);

        $this->assertSame(
            [
                'type' => 'bigint',
                'length' => 20,
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

    public function testAddColumnTypeBinary(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'binary',
        ]);

        $this->assertSame(
            [
                'type' => 'binary',
                'length' => 1,
                'precision' => null,
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

    public function testAddColumnTypeBlob(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'blob',
        ]);

        $this->assertSame(
            [
                'type' => 'blob',
                'length' => 65535,
                'precision' => null,
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

    public function testAddColumnTypeChar(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'char',
        ]);

        $this->assertSame(
            [
                'type' => 'char',
                'length' => 1,
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

    public function testAddColumnTypeDateTime(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'datetime',
        ]);

        $this->assertSame(
            [
                'type' => 'datetime',
                'length' => null,
                'precision' => null,
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

    public function testAddColumnTypeDecimal(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'decimal',
            'length' => 10,
            'precision' => 2,
        ]);

        $this->assertSame(
            [
                'type' => 'decimal',
                'length' => 10,
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

    public function testAddColumnTypeDouble(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'double',
        ]);

        $this->assertSame(
            [
                'type' => 'double',
                'length' => null,
                'precision' => null,
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

    public function testAddColumnTypeEnum(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'enum',
            'values' => [
                'Y',
                'N',
            ],
        ]);

        $this->assertSame(
            [
                'type' => 'enum',
                'length' => null,
                'precision' => null,
                'values' => ['Y', 'N'],
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

    public function testAddColumnTypeFloat(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'float',
        ]);

        $this->assertSame(
            [
                'type' => 'float',
                'length' => null,
                'precision' => null,
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

    public function testAddColumnTypeInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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

    public function testAddColumnTypeLongText(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'longtext',
        ]);

        $this->assertSame(
            [
                'type' => 'longtext',
                'length' => 4294967295,
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

    public function testAddColumnTypeMediumInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'mediumint',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'mediumint',
        ]);

        $this->assertSame(
            [
                'type' => 'mediumint',
                'length' => 8,
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

    public function testAddColumnTypeMediumText(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'mediumtext',
        ]);

        $this->assertSame(
            [
                'type' => 'mediumtext',
                'length' => 16777215,
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

    public function testAddColumnTypeSmallInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'smallint',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'smallint',
        ]);

        $this->assertSame(
            [
                'type' => 'smallint',
                'length' => 6,
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

    public function testAddColumnTypeText(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'text',
        ]);

        $this->assertSame(
            [
                'type' => 'text',
                'length' => 65535,
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

    public function testAddColumnTypeTinyInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'tinyint',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'tinyint',
        ]);

        $this->assertSame(
            [
                'type' => 'tinyint',
                'length' => 4,
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

    public function testAddColumnTypeTinyText(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'tinytext',
        ]);

        $this->assertSame(
            [
                'type' => 'tinytext',
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

    public function testAddColumnUnsigned(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
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
