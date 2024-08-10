<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

trait AddColumnTestTrait
{
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
                'nullable' => false,
                'unsigned' => false,
                'default' => '1',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 9,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'varchar',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'decimal',
                'length' => 11,
                'precision' => 2,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'bigint',
                'length' => 20,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'char',
                'length' => 1,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'datetime',
                'length' => null,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'decimal',
                'length' => 10,
                'precision' => 2,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'double',
                'length' => null,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'float',
                'length' => null,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 11,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'mediumint',
                'length' => 8,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'smallint',
                'length' => 6,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'tinyint',
                'length' => 4,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
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
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'length' => 10,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => true,
                'default' => 'NULL',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }
}
