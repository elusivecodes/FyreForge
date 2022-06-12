<?php
declare(strict_types=1);

namespace Tests\Forge;

trait DropIndexTest
{

    public function testDropIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->addIndex('test', 'id');

        $this->forge->dropIndex('test', 'id');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasIndex('id')
        );
    }

    public function testDropIndexSql(): void
    {
        $this->assertSame(
            'DROP INDEX value ON test',
            $this->forge->dropIndexSql('test', 'value')
        );
    }

}
