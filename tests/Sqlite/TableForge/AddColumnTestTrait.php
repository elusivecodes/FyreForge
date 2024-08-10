<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddColumnTestTrait
{
    public function testAddColumnExistingColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'int',
            ]);
    }

    public function testAddColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL',
            ],
            $this->forge
                ->build('test')
                ->addColumn('value', [
                    'type' => 'int',
                ])
                ->sql()
        );
    }

    public function testAddColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->sql()
        );
    }
}
