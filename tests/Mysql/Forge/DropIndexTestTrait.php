<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

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

    public function testDropPrimaryKey(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->addIndex('test', 'PRIMARY', [
            'columns' => ['id'],
            'primary' => true,
        ]);

        $this->forge->dropPrimaryKey('test');

        $this->assertNull(
            $this->schema->describe('test')
                ->primaryKey()
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
