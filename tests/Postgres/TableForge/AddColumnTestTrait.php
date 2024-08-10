<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddColumnTestTrait
{
    public function testAddColumnExistingColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'integer',
            ]);
    }

    public function testAddColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ADD COLUMN value INTEGER NOT NULL',
            ],
            $this->forge
                ->build('test')
                ->addColumn('value', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testAddColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INTEGER NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }
}
