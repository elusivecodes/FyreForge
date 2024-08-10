<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait DropTableTestTrait
{
    public function testDropTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'integer',
            ],
        ]);

        $this->forge->dropTable('test');

        $this->assertFalse(
            $this->schema->hasTable('test')
        );
    }
}
