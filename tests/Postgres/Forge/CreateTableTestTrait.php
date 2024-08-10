<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait CreateTableTestTrait
{
    public function testCreateTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
                'autoIncrement' => true,
            ],
            'value' => [
                'type' => 'character varying',
                'length' => 255,
            ],
            'created' => [
                'type' => 'timestamp',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'modified' => [
                'type' => 'timestamp',
                'nullable' => true,
                'default' => 'NULL',
            ],
        ], [
            'comment' => 'This is the value',
            'indexes' => [
                'test_pkey' => [
                    'columns' => [
                        'id',
                    ],
                    'primary' => true,
                ],
            ],
        ]);

        $this->assertTrue(
            $this->schema->hasTable('test')
        );

        $this->assertSame(
            [
                'comment' => 'This is the value',
            ],
            $this->schema->table('test')
        );
    }
}
