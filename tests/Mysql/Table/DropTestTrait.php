<?php
declare(strict_types=1);

namespace Tests\Mysql\Table;

use Fyre\DB\Types\IntegerType;
use Fyre\Forge\Exceptions\ForgeException;

trait DropTestTrait
{
    public function testDropNewTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->addColumn('id', [
                'type' => IntegerType::class,
            ])
            ->drop();
    }

    public function testDropSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
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
