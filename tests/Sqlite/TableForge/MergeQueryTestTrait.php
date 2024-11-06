<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

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

        $this->assertSame(
            [
                'DROP INDEX test_idx',
                'ALTER TABLE test DROP COLUMN test',
                'ALTER TABLE test ADD COLUMN value INTEGER NOT NULL',
                'CREATE INDEX id ON test (id)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('value', [
                    'type' => IntegerType::class,
                ])
                ->dropColumn('test')
                ->dropIndex('test_idx')
                ->addIndex('id')
                ->sql()
        );
    }
}
