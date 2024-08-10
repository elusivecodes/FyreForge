<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

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
                'test',
            ],
        ]);

        $this->forge
            ->build('test')
            ->changeColumn('id', [
                'type' => 'int',
            ])
            ->addColumn('value', [
                'type' => 'int',
            ])
            ->dropColumn('test')
            ->dropIndex('test')
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
                ->hasColumn('test')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }
}
