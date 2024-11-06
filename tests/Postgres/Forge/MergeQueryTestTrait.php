<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;

trait MergeQueryTestTrait
{
    public function testMergeQueries(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => StringType::class,
            ],
            'test' => [
                'type' => StringType::class,
            ],
        ], [
            'indexes' => [
                'test_idx' => [
                    'columns' => ['test'],
                ],
            ],
        ]);

        $this->forge
            ->build('test')
            ->changeColumn('id', [
                'type' => IntegerType::class,
            ])
            ->addColumn('value', [
                'type' => IntegerType::class,
            ])
            ->dropColumn('test')
            ->dropIndex('test_idx')
            ->addIndex('id')
            ->execute();

        $this->assertSame(
            'integer',
            $this->schema->describe('test')
                ->column('id')['type']
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('test')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }
}
