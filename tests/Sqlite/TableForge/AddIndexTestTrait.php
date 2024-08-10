<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait AddIndexTestTrait
{
    public function testAddIndexExistingIndex(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);
        $this->forge->addIndex('test', 'id');

        $this->forge
            ->build('test')
            ->addIndex('id');
    }

    public function testAddIndexSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->assertSame(
            [
                'CREATE INDEX id ON test (id)',
            ],
            $this->forge
                ->build('test')
                ->addIndex('id')
                ->sql()
        );
    }

    public function testAddIndexSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL)',
                'CREATE INDEX id ON test (id)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addIndex('id')
                ->sql()
        );
    }
}
