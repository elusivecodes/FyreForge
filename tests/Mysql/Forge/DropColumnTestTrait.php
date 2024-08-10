<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait DropColumnTestTrait
{
    public function testDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->dropColumn('test', 'value');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }
}
