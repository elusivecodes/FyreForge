<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

trait MergeQueryTestTrait
{
    public function testMergeQueries(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'varchar',
            ],
            'test' => [
                'type' => 'varchar',
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
                'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL',
                'CREATE INDEX id ON test (id)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('value', [
                    'type' => 'int',
                ])
                ->dropColumn('test')
                ->dropIndex('test_idx')
                ->addIndex('id')
                ->sql()
        );
    }
}
