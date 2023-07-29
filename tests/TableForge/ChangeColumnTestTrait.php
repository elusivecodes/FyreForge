<?php
declare(strict_types=1);

namespace Tests\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait ChangeColumnTestTrait
{

    public function testChangeColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'varchar'
                ])
                ->changeColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testChangeColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'varchar'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test CHANGE COLUMN id id INT(11) NOT NULL'
            ],
            $this->forge
                ->build('test')
                ->changeColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testChangeColumnSqlInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->changeColumn('invalid', [
                'type' => 'int'
            ]);
    }

}
