<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

use Fyre\DB\Types\IntegerType;

trait RenameColumnTestTrait
{
    public function testRenameColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
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
