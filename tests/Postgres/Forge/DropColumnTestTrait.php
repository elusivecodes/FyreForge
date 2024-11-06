<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

use Fyre\DB\Types\IntegerType;

trait DropColumnTestTrait
{
    public function testDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->dropColumn('test', 'value');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }
}
