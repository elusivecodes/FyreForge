<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

use Fyre\DB\Types\DateTimeType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait CreateTableTestTrait
{
    public function testCreateTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => StringType::class,
                'length' => 255,
            ],
            'created' => [
                'type' => DateTimeType::class,
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'modified' => [
                'type' => DateTimeType::class,
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'comment' => 'This is the value',
            'indexes' => [
                'test_pkey' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->assertTrue(
            $this->schema->hasTable('test')
        );

        $this->assertSame(
            [
                'comment' => 'This is the value',
            ],
            $this->schema->table('test')
        );
    }
}
