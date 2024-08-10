<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropTestTrait
{
    public function testDropNewTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'integer',
            ])
            ->drop();
    }

    public function testDropSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
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
