<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait AddColumnTestTrait
{
    public function testAddColumnComment(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'character varying',
            'comment' => 'This is the value',
        ]);

        $this->assertSame(
            [
                'type' => 'character varying',
                'length' => 80,
                'precision' => null,
                'nullable' => false,
                'default' => null,
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
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'integer',
            'default' => '1',
        ]);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => 11,
                'precision' => 0,
                'nullable' => false,
                'default' => '1',
                'comment' => '',
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
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'character varying',
            'length' => 255,
        ]);

        $this->assertSame(
            [
                'type' => 'character varying',
                'length' => 255,
                'precision' => null,
                'nullable' => false,
                'default' => null,
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
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'integer',
            'nullable' => true,
        ]);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => 11,
                'precision' => 0,
                'nullable' => true,
                'default' => null,
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
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'numeric',
            'precision' => 2,
        ]);

        $this->assertSame(
            [
                'type' => 'numeric',
                'length' => 10,
                'precision' => 2,
                'nullable' => false,
                'default' => null,
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
                'type' => 'integer',
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
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeBytea(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'bytea',
        ]);

        $this->assertSame(
            [
                'type' => 'bytea',
                'length' => null,
                'precision' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeCharacter(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'character',
        ]);

        $this->assertSame(
            [
                'type' => 'character',
                'length' => 1,
                'precision' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeDoublePrecision(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'double precision',
        ]);

        $this->assertSame(
            [
                'type' => 'double precision',
                'length' => null,
                'precision' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeInteger(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'integer',
        ]);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => 11,
                'precision' => 0,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeNumeric(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'numeric',
            'length' => 10,
            'precision' => 2,
        ]);

        $this->assertSame(
            [
                'type' => 'numeric',
                'length' => 10,
                'precision' => 2,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeReal(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'real',
        ]);

        $this->assertSame(
            [
                'type' => 'real',
                'length' => null,
                'precision' => null,
                'nullable' => false,
                'default' => null,
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
                'nullable' => false,
                'default' => null,
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
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'text',
        ]);

        $this->assertSame(
            [
                'type' => 'text',
                'length' => null,
                'precision' => null,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAddColumnTypeTimestamp(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'timestamp',
        ]);

        $this->assertSame(
            [
                'type' => 'timestamp without time zone',
                'length' => null,
                'precision' => 6,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }
}
