<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait DropColumnTestTrait
{
    public function testDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
            'value' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->dropColumn('test', 'value');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }
}
