<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

use Fyre\DB\Exceptions\DbException;

trait MergeQueryTestTrait
{
    public function testMergeQueries(): void
    {
        // not supported with sqlite version bundled with PHP
        $this->expectException(DbException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
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

        $this->forge
            ->build('test')
            ->addColumn('value', [
                'type' => 'int',
                'nullable' => true,
                'default' => 'NULL',
            ])
            ->dropColumn('test')
            ->dropIndex('test_idx')
            ->addIndex('id')
            ->execute();

        $this->assertSame(
            'int',
            $this->schema->describe('test')
                ->column('id')['type']
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasIndex('test_idx')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }
}
