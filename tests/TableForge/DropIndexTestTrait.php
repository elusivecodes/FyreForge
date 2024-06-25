<?php
declare(strict_types=1);

namespace Tests\TableForge;

use Fyre\Forge\Exceptions\ForgeException;

trait DropIndexTestTrait
{
    public function testDropIndexSqlExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ], [
            'indexes' => [
                'id',
            ],
        ]);

        $this->assertSame(
            [
                'DROP INDEX id ON test',
            ],
            $this->forge
                ->build('test')
                ->dropIndex('id')
                ->sql()
        );
    }

    public function testDropIndexSqlInvalidIndex(): void
    {
        $this->expectException(ForgeException::class);

        $this->forge
            ->build('test')
            ->dropIndex('invalid');
    }

    public function testDropIndexSqlNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            ],
            $this->forge
                ->build('test')
                ->addColumn('id', [
                    'type' => 'int',
                ])
                ->addIndex('id')
                ->dropIndex('id')
                ->sql()
        );
    }
}
