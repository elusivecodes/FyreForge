<?php
declare(strict_types=1);

namespace Tests\Mysql\TableForge;

use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;
use Fyre\Forge\Exceptions\ForgeException;

trait ChangeColumnTestTrait
{
    public function testChangeColumnInvalidColumn(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->changeColumn('invalid', [
                'type' => IntegerType::class,
            ]);
    }

    public function testChangeColumnSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => StringType::class,
            ],
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test CHANGE COLUMN id id INT(11) NOT NULL',
            ],
            $this->forge
                ->build('test')
                ->changeColumn('id', [
                    'type' => IntegerType::class,
                ])
                ->sql()
        );
    }

    public function testChangeColumnSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => StringType::class,
                ])
                ->changeColumn('id', [
                    'type' => IntegerType::class,
                ])
                ->sql()
        );
    }
}
