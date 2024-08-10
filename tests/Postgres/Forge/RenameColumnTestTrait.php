<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait RenameColumnTestTrait
{
    public function testRenameColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->renameColumn('test', 'value', 'other');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('other')
        );
    }
}
