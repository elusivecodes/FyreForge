<?php
declare(strict_types=1);

namespace Tests\Forge;

trait RenameTableTestTrait
{
    public function testRenameTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->renameTable('test', 'other');

        $this->assertFalse(
            $this->schema->hasTable('test')
        );

        $this->assertTrue(
            $this->schema->hasTable('other')
        );
    }

    public function testRenameTableSql(): void
    {
        $this->assertSame(
            'RENAME TABLE test TO other',
            $this->forge->renameTableSql('test', 'other')
        );
    }
}
