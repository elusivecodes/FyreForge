<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

trait TableTestTrait
{
    public function testOptionsExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [
                'COMMENT ON TABLE test IS \'This is the value\'',
            ],
            $this->forge
                ->build('test', [
                    'comment' => 'This is the value',
                ])
                ->sql()
        );
    }

    public function testOptionsNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INTEGER NOT NULL)',
                'COMMENT ON TABLE test IS \'This is the value\'',
            ],
            $this->forge
                ->build('test', [
                    'comment' => 'This is the value',
                ])
                ->addColumn('id', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }
}