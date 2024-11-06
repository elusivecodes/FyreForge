<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait AddIndexTestTrait
{
    public function testAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addIndex('test', 'id_value', [
            'columns' => ['id', 'value'],
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id_value')
        );

        $this->assertSame(
            [
                'columns' => ['id', 'value'],
                'unique' => false,
                'primary' => false,
                'type' => 'btree',
            ],
            $this->schema->describe('test')
                ->index('id_value'),
        );
    }

    public function testAddIndexFulltext(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->forge->addIndex('test', 'value', [
            'type' => 'FULLTEXT',
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('value')
        );

        $this->assertSame(
            [
                'columns' => ['value'],
                'unique' => false,
                'primary' => false,
                'type' => 'fulltext',
            ],
            $this->schema->describe('test')
                ->index('value'),
        );
    }

    public function testAddIndexPrimary(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addIndex('test', 'PRIMARY', [
            'columns' => ['id'],
        ]);

        $this->assertSame(
            [
                'id',
            ],
            $this->schema->describe('test')
                ->primaryKey(),
        );
    }

    public function testAddIndexUnique(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addIndex('test', 'value', [
            'unique' => true,
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('value')
        );

        $this->assertSame(
            [
                'columns' => ['value'],
                'unique' => true,
                'primary' => false,
                'type' => 'btree',
            ],
            $this->schema->describe('test')
                ->index('value'),
        );
    }
}
