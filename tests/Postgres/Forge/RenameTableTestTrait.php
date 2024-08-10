<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait RenameTableTestTrait
{
    public function testRenameTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
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
}
