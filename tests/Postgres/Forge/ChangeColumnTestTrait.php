<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait ChangeColumnTestTrait
{
    public function testAlterColumnAutoIncrement(): void
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
                'test_pk' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->forge->alterColumnAutoIncrement('test', 'id', true);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => 11,
                'precision' => 0,
                'nullable' => false,
                'default' => null,
                'comment' => '',
                'autoIncrement' => true,
            ],
            $this->schema->describe('test')
                ->column('id')
        );
    }

    public function testAlterColumnAutoIncrementFalse(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ], [
            'indexes' => [
                'test_pk' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->forge->alterColumnAutoIncrement('test', 'id', false);

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
                ->column('id')
        );
    }

    public function testAlterColumnDefault(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->alterColumnDefault('test', 'value', '1');

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

    public function testAlterColumnDefaultNull(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
                'default' => '1',
            ],
        ]);

        $this->forge->alterColumnDefault('test', 'value', null);

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

    public function testAlterColumnNullable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->alterColumnNullable('test', 'value', true);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => 11,
                'precision' => 0,
                'nullable' => true,
                'default' => 'NULL',
                'comment' => '',
                'autoIncrement' => false,
            ],
            $this->schema->describe('test')
                ->column('value')
        );
    }

    public function testAlterColumnNullableFalse(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
                'nullable' => true,
            ],
        ]);

        $this->forge->alterColumnNullable('test', 'value', false);

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

    public function testAlterColumnType(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->alterColumnType('test', 'value', [
            'type' => IntegerType::class,
            'cast' => true,
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

    public function testCommentOnColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->commentOnColumn('test', 'value', 'This is the value');

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
}
