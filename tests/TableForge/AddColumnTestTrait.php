<?php
declare(strict_types=1);

namespace Tests\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddColumnTestTrait
{

    public function testAddColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testAddColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL AFTER id'
            ],
            $this->forge
                ->build('test')
                ->addColumn('value', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testAddColumnSqlExistingColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'int'
            ]);
    }

}
