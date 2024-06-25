<?php
declare(strict_types=1);

namespace Tests\Forge;

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

    public function testDropTableSql(): void
    {
        $this->assertSame(
            'DROP TABLE test',
            $this->forge->dropTableSql('test')
        );
    }

    public function testDropTableSqlIfExists(): void
    {
        $this->assertSame(
            'DROP TABLE IF EXISTS test',
            $this->forge->dropTableSql('test', ['ifExists' => true])
        );
    }
}
