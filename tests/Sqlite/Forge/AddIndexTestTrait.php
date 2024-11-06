<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

use Fyre\DB\Types\IntegerType;
use Fyre\Forge\Exceptions\ForgeException;

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
            ],
            $this->schema->describe('test')
                ->index('id_value'),
        );
    }

    public function testAddIndexPrimary(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->addIndex('test', 'primary', [
            'columns' => ['id'],
            'primary' => true,
        ]);
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
            ],
            $this->schema->describe('test')
                ->index('value'),
        );
    }
}
