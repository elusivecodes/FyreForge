<?php
declare(strict_types=1);

namespace Tests\TableForge;

use
    Fyre\Forge\Exceptions\ForgeException;

trait DropTest
{

    public function testDropSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'DROP TABLE test'
            ],
            $this->forge
                ->build('test')
                ->drop()
                ->sql()
        );
    }

    public function testDropSqlNewTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => 'int'
            ])
            ->drop();
    }

}
