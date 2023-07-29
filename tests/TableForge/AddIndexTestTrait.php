<?php
declare(strict_types=1);

namespace Tests\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddIndexTestTrait
{

    public function testAddIndexSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL, INDEX id (id) USING BTREE) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addIndex('id')
                ->sql()
        );
    }

    public function testAddIndexSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD INDEX id (id) USING BTREE'
            ],
            $this->forge
                ->build('test')
                ->addIndex('id')
                ->sql()
        );
    }

    public function testAddIndexSqlExistingIndex(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ], [
            'indexes' => [
                'id'
            ]
        ]);

        $this->forge
            ->build('test')
            ->addIndex('id');
    }

}
