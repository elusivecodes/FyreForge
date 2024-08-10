<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait ChangeColumnTestTrait
{
    public function testChangeColumnInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->changeColumn('invalid', [
                'type' => 'int',
            ]);
    }

    public function testChangeColumnSqlExistingTable(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => 'varchar',
            ],
        ]);

        $this->forge
            ->build('test')
            ->changeColumn('id', [
                'type' => 'int',
            ])
            ->sql();
    }

    public function testChangeColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'varchar',
                ])
                ->changeColumn('id', [
                    'type' => 'int',
                ])
                ->sql()
        );
    }
}
