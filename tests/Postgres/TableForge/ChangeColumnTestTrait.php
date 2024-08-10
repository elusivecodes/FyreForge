<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait ChangeColumnTestTrait
{
    public function testChangeColumnInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->changeColumn('invalid', [
                'type' => 'integer',
            ]);
    }

    public function testChangeColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'varchar',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ALTER COLUMN id TYPE INTEGER USING CAST(id AS INTEGER)',
            ],
            $this->forge
                ->build('test')
                ->changeColumn('id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testChangeColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INTEGER NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'varchar',
                ])
                ->changeColumn('id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }
}
