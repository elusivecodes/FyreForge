<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

use Fyre\DB\Types\DateTimeType;
use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\FloatType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait AddColumnTestTrait
{
    public function testAddColumnDefault(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'default' => '1',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'integer',
                'length' => null,
                'precision' => 0,
                'nullable' => false,
                'unsigned' => false,
                'default' => '1',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnLength(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'length' => 9,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'int',
                'length' => 9,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnLengthVarchar(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => StringType::class,
            'length' => 255,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'varchar',
                'length' => 255,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnPrecision(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => DecimalType::class,
            'precision' => 2,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'numeric',
                'length' => 11,
                'precision' => 2,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeBigInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'length' => 20,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'bigint',
                'length' => 20,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeChar(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => StringType::class,
            'length' => 1,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'char',
                'length' => 1,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeDateTime(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => DateTimeType::class,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'datetime',
                'length' => null,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeDecimal(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => DecimalType::class,
            'length' => 10,
            'precision' => 2,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'numeric',
                'length' => 10,
                'precision' => 2,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeFloat(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => FloatType::class,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'real',
                'length' => null,
                'precision' => null,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'integer',
                'length' => null,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeMediumInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'length' => 8,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'mediumint',
                'length' => 8,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeSmallInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'length' => 6,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'smallint',
                'length' => 6,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnTypeTinyInt(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'length' => 4,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'tinyint',
                'length' => 4,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => false,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }

    public function testAddColumnUnsigned(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => IntegerType::class,
            'unsigned' => true,
            'nullable' => true,
            'default' => 'NULL',
        ]);

        $this->assertSame(
            [
                'name' => 'value',
                'type' => 'integer',
                'length' => null,
                'precision' => 0,
                'nullable' => true,
                'unsigned' => true,
                'default' => 'NULL',
                'comment' => null,
                'autoIncrement' => false,
            ],
            $this->schema->table('test')
                ->column('value')
                ->toArray()
        );
    }
}
