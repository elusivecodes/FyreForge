<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait AlterTableTestTrait
{
    public function testCommentOnTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int',
            ],
        ]);

        $this->forge->commentOnTable('test', 'This is the value');

        $this->assertSame(
            [
                'comment' => 'This is the value',
            ],
            $this->schema->table('test')
        );
    }
}
