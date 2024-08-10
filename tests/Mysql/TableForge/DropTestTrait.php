<?php
declare(strict_types=1);

namespace Tests\Mysql\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropTestTrait
{
    public function testDropNewTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'int',
            ])
            ->drop();
    }

    public function testDropSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->assertSame(
            [
                'DROP TABLE test',
            ],
            $this->forge
                ->build('test')
                ->drop()
                ->sql()
        );
    }
}
