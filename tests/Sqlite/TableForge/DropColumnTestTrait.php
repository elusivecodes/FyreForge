<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropColumnTestTrait
{
    public function testDropColumnInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->dropColumn('invalid');
    }

    public function testDropColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP COLUMN value',
            ],
            $this->forge
                ->build('test')
                ->dropColumn('value')
                ->sql()
        );
    }

    public function testDropColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL)',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addColumn('value', [
                    'type' => 'int',
                ])
                ->dropColumn('value')
                ->sql()
        );
    }
}
