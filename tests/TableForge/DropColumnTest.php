<?php
declare(strict_types=1);

namespace Tests\TableForge;

use
    Fyre\Forge\Exceptions\ForgeException;

trait DropColumnTest
{

    public function testDropColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->addColumn('value', [
                    'type' => 'int'
                ])
                ->dropColumn('value')
                ->sql()
        );
    }

    public function testDropColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test DROP COLUMN value'
            ],
            $this->forge
                ->build('test')
                ->dropColumn('value')
                ->sql()
        );
    }

    public function testDropColumnSqlInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->dropColumn('invalid');
    }

}
