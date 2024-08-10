<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropIndexTestTrait
{
    public function testDropIndexInvalidIndex(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->dropIndex('invalid');
    }

    public function testDropIndexSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);
        $this->forge->addIndex('test', 'id');

        $this->assertSame(
            [
                'DROP INDEX id',
            ],
            $this->forge
                ->build('test')
                ->dropIndex('id')
                ->sql()
        );
    }

    public function testDropIndexSqlNewTable(): void
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
                ->addIndex('id')
                ->dropIndex('id')
                ->sql()
        );
    }
}
