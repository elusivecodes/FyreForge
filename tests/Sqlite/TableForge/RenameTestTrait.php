<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

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
                'CREATE TABLE other (id INT(11) NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->rename('other')
                ->sql()
        );
    }
}
