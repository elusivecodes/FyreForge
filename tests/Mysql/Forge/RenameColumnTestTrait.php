<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait RenameColumnTestTrait
{
    public function testRenameColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
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
