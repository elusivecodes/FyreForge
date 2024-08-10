<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

trait MergeQueryTestTrait
{
    public function testMergeQueries(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'character varying',
            ],
            'test' => [
                'type' => 'character varying',
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
                'ALTER TABLE test DROP COLUMN test, ALTER COLUMN id TYPE INTEGER USING CAST(id AS INTEGER), ADD COLUMN value INTEGER NOT NULL',
                'CREATE INDEX id ON test USING BTREE (id)',
            ],
            $this->forge
                ->build('test')
                ->changeColumn('id', [
                    'type' => 'integer',
                ])
                ->addColumn('value', [
                    'type' => 'integer',
                ])
                ->dropColumn('test')
                ->dropIndex('test_idx')
                ->addIndex('id')
                ->sql()
        );
    }
}
