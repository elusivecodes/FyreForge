<?php
declare(strict_types=1);

namespace Tests\Forge;

trait DropColumnTestTrait
{

    public function testDropColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->dropColumn('test', 'value');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }

    public function testDropColumnSql(): void
    {
        $this->assertSame(
            'ALTER TABLE test DROP COLUMN value',
            $this->forge->dropColumnSql('test', 'value')
        );
    }

    public function testDropColumnSqlIfExists(): void
    {
        $this->assertSame(
            'ALTER TABLE test DROP COLUMN IF EXISTS value',
            $this->forge->dropColumnSql('test', 'value', ['ifExists' => true])
        );
    }

}
