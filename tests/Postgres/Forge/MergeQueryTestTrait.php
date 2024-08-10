<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

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
