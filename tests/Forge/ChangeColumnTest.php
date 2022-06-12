<?php
declare(strict_types=1);

namespace Tests\Forge;

trait ChangeColumnTest
{

    public function testChangeColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->changeColumn('test', 'value', [
            'name' => 'other',
            'type' => 'int'
        ]);

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('other')
        );
    }

    public function testChangeColumnSql(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value other INT(11) NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'name' => 'other',
                'type' => 'int'
            ])
        );
    }

    public function testChangeColumnSqlLength(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(10) NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'length' => 9
            ])
        );
    }

    public function testChangeColumnSqlLengthVarchar(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value VARCHAR(255) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'varchar',
                'length' => 255
            ])
        );
    }

    public function testChangeColumnSqlPrecision(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value DECIMAL(11,2) NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'decimal',
                'precision' => 2
            ])
        );
    }

    public function testChangeColumnSqlUnsigned(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(10) UNSIGNED NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'unsigned' => true
            ])
        );
    }

    public function testChangeColumnSqlCharsetCollation(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value VARCHAR(80) CHARACTER SET \'utf8\' COLLATE \'utf8_unicode_ci\' NOT NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'varchar',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
        );
    }

    public function testChangeColumnSqlNullable(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(11) NULL',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'nullable' => true
            ])
        );
    }

    public function testChangeColumnSqlDefault(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(11) NOT NULL DEFAULT 1',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'default' => '1'
            ])
        );
    }

    public function testChangeColumnSqlExtra(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN id id INT(11) NOT NULL AUTO_INCREMENT',
            $this->forge->changeColumnSql('test', 'id', [
                'type' => 'int',
                'extra' => 'AUTO_INCREMENT'
            ])
        );
    }

    public function testChangeColumnSqlComment(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(11) NOT NULL COMMENT \'This is the value\'',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'comment' => 'This is the value'
            ])
        );
    }

    public function testChangeColumnSqlAfter(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN value value INT(11) NOT NULL AFTER id',
            $this->forge->changeColumnSql('test', 'value', [
                'type' => 'int',
                'after' => 'id'
            ])
        );
    }

    public function testChangeColumnSqlFirst(): void
    {
        $this->assertSame(
            'ALTER TABLE test CHANGE COLUMN id id INT(11) NOT NULL FIRST',
            $this->forge->changeColumnSql('test', 'id', [
                'type' => 'int',
                'first' => true
            ])
        );
    }

}
