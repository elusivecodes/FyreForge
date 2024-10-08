<?php
declare(strict_types=1);

namespace Tests\Mysql\TableForge;

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

        $this->assertSame(
            [
                'ALTER TABLE test DROP INDEX test, DROP COLUMN test, CHANGE COLUMN id id INT(11) NOT NULL, ADD COLUMN value INT(11) NOT NULL AFTER id, ADD INDEX id (id) USING BTREE',
            ],
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
                ->sql()
        );
    }
}
