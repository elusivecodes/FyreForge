<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

trait DropTableTestTrait
{
    public function testDropTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->dropTable('test');

        $this->assertFalse(
            $this->schema->hasTable('test')
        );
    }
}
