<?php
declare(strict_types=1);

namespace Tests\Forge;

trait AddColumnTestTrait
{

    public function testAddColumn(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->addColumn('test', 'value', [
            'type' => 'decimal',
            'length' => 10,
            'precision' => 2,
            'nullable' => true,
            'unsigned' => true,
            'default' => 'NULL',
            'comment' => 'Test Value'
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }

    public function testAddColumnSql(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value VARCHAR(80) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value')
        );
    }

    public function testAddColumnSqlTinyInt(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value TINYINT(4) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'tinyint'
            ])
        );
    }

    public function testAddColumnSqlSmallInt(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value SMALLINT(6) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'smallint'
            ])
        );
    }

    public function testAddColumnSqlMediumInt(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value MEDIUMINT(8) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'mediumint'
            ])
        );
    }

    public function testAddColumnSqlInt(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int'
            ])
        );
    }

    public function testAddColumnSqlBigInt(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value BIGINT(20) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'bigint'
            ])
        );
    }

    public function testAddColumnSqlDecimal(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value DECIMAL(11) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'decimal'
            ])
        );
    }

    public function testAddColumnSqlFloat(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value FLOAT NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'float'
            ])
        );
    }

    public function testAddColumnSqlDouble(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value DOUBLE NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'double'
            ])
        );
    }

    public function testAddColumnSqlChar(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value CHAR CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'char'
            ])
        );
    }

    public function testAddColumnSqlVarChar(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value VARCHAR(80) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'varchar'
            ])
        );
    }

    public function testAddColumnSqlTinyText(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value TINYTEXT CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'tinytext'
            ])
        );
    }

    public function testAddColumnSqlMediumText(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value MEDIUMTEXT CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'mediumtext'
            ])
        );
    }

    public function testAddColumnSqlLongText(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value LONGTEXT CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'longtext'
            ])
        );
    }

    public function testAddColumnSqlEnum(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value ENUM(\'Y\',\'N\') CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'enum',
                'values' => [
                    'Y',
                    'N'
                ]
            ])
        );
    }

    public function testAddColumnSqlSet(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value SET(\'Value A\',\'Value B\') CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'set',
                'values' => [
                    'Value A',
                    'Value B'
                ]
            ])
        );
    }

    public function testAddColumnSqlBinary(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value BINARY NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'binary'
            ])
        );
    }

    public function testAddColumnSqlDateTime(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value DATETIME NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'datetime'
            ])
        );
    }

    public function testAddColumnSqlLength(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(9) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'length' => 9
            ])
        );
    }

    public function testAddColumnSqlLengthVarchar(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value VARCHAR(255) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'varchar',
                'length' => 255
            ])
        );
    }

    public function testAddColumnSqlPrecision(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value DECIMAL(11,2) NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'decimal',
                'precision' => 2
            ])
        );
    }

    public function testAddColumnSqlUnsigned(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(10) UNSIGNED NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'unsigned' => true
            ])
        );
    }

    public function testAddColumnSqlCharsetCollation(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value VARCHAR(80) CHARACTER SET \'utf8\' COLLATE \'utf8_unicode_ci\' NOT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'varchar',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
        );
    }

    public function testAddColumnSqlNullable(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'nullable' => true
            ])
        );
    }

    public function testAddColumnSqlDefault(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL DEFAULT 1',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'default' => '1'
            ])
        );
    }

    public function testAddColumnSqlDefaultLiteral(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'datetime',
                'default' => 'CURRENT_TIMESTAMP'
            ])
        );
    }

    public function testAddColumnSqlDefaultNull(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NULL DEFAULT NULL',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'nullable' => true,
                'default' => 'NULL'
            ])
        );
    }

    public function testAddColumnSqlDefaultString(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value VARCHAR(80) CHARACTER SET \'utf8mb4\' COLLATE \'utf8mb4_unicode_ci\' NOT NULL DEFAULT \'Value\'',
            $this->forge->addColumnSql('test', 'value', [
                'default' => '\'Value\''
            ])
        );
    }

    public function testAddColumnSqlExtra(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN id INT(11) NOT NULL AUTO_INCREMENT',
            $this->forge->addColumnSql('test', 'id', [
                'type' => 'int',
                'extra' => 'AUTO_INCREMENT'
            ])
        );
    }

    public function testAddColumnSqlComment(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL COMMENT \'This is the value\'',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'comment' => 'This is the value'
            ])
        );
    }

    public function testAddColumnSqlAfter(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN value INT(11) NOT NULL AFTER id',
            $this->forge->addColumnSql('test', 'value', [
                'type' => 'int',
                'after' => 'id'
            ])
        );
    }

    public function testAddColumnSqlFirst(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD COLUMN id INT(11) NOT NULL FIRST',
            $this->forge->addColumnSql('test', 'id', [
                'type' => 'int',
                'first' => true
            ])
        );
    }

}
