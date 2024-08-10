<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

trait DropIndexTestTrait
{
    public function testDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addIndex('test', 'id');

        $this->forge->dropIndex('test', 'id');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }

    public function testDropUniqueKey(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addIndex('test', 'id', [
            'unique' => true,
        ]);

        $this->forge->dropIndex('test', 'id');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }
}
