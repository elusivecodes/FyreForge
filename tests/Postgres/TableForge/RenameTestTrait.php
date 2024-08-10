<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

trait RenameTestTrait
{
    public function testRenameSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test RENAME TO other',
            ],
            $this->forge
                ->build('test')
                ->rename('other')
                ->sql()
        );
    }

    public function testRenameSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE other (id INTEGER NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->rename('other')
                ->sql()
        );
    }
}
