<?php
declare(strict_types=1);

namespace Tests\Mysql\TableForge;

use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class TableForgeTest extends TestCase
{
    use AddColumnTestTrait;
    use AddForeignKeyTestTrait;
    use AddIndexTestTrait;
    use ChangeColumnTestTrait;
    use DiffDefaultsTestTrait;
    use DiffTestTrait;
    use DropColumnTestTrait;
    use DropForeignKeyTestTrait;
    use DropIndexTestTrait;
    use DropTestTrait;
    use ExecuteTestTrait;
    use MergeQueryTestTrait;
    use MysqlConnectionTrait;
    use RenameTestTrait;
    use TableTestTrait;

    public function testColumn(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value', [
            'type' => 'varchar',
        ]);

        $this->assertSame(
            [
                'type' => 'int',
                'charset' => null,
                'collation' => null,
                'length' => 11,
                'precision' => 0,
                'nullable' => false,
                'default' => null,
                'autoIncrement' => false,
                'unsigned' => false,
                'comment' => '',
                'values' => null,
            ],
            $tableForge->column('id')
        );
    }

    public function testColumnNames(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value', [
            'type' => 'varchar',
        ]);

        $this->assertSame(
            [
                'id',
                'value',
            ],
            $tableForge->columnNames()
        );
    }

    public function testColumns(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value', [
            'type' => 'varchar',
        ]);

        $this->assertSame(
            [
                'id' => [
                    'type' => 'int',
                    'charset' => null,
                    'collation' => null,
                    'length' => 11,
                    'precision' => 0,
                    'nullable' => false,
                    'default' => null,
                    'autoIncrement' => false,
                    'unsigned' => false,
                    'comment' => '',
                    'values' => null,
                ],
                'value' => [
                    'type' => 'varchar',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'length' => 80,
                    'precision' => null,
                    'nullable' => false,
                    'default' => null,
                    'autoIncrement' => false,
                    'unsigned' => false,
                    'comment' => '',
                    'values' => null,
                ],
            ],
            $tableForge->columns()
        );
    }

    public function testForeignKey(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'int',
        ]);

        $tableForge->addForeignKey('value_id', [
            'referencedTable' => 'test_values',
            'referencedColumns' => 'id',
        ]);

        $this->assertSame(
            [
                'referencedTable' => 'test_values',
                'referencedColumns' => [
                    'id',
                ],
                'columns' => [
                    'value_id',
                ],
                'update' => '',
                'delete' => '',
            ],
            $tableForge->foreignKey('value_id')
        );
    }

    public function testForeignKeys(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'int',
        ]);

        $tableForge->addForeignKey('value_id', [
            'referencedTable' => 'test_values',
            'referencedColumns' => 'id',
        ]);

        $this->assertSame(
            [
                'value_id' => [
                    'referencedTable' => 'test_values',
                    'referencedColumns' => [
                        'id',
                    ],
                    'columns' => [
                        'value_id',
                    ],
                    'update' => '',
                    'delete' => '',
                ],
            ],
            $tableForge->foreignKeys()
        );
    }

    public function testGetTableName(): void
    {
        $tableForge = $this->forge->build('test');

        $this->assertSame(
            'test',
            $tableForge->getTableName()
        );
    }

    public function testHasColumn(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $this->assertTrue(
            $tableForge->hasColumn('id')
        );
    }

    public function testHasColumnFalse(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $this->assertFalse(
            $tableForge->hasColumn('invalid')
        );
    }

    public function testHasForeignKey(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'int',
        ]);

        $tableForge->addForeignKey('value_id', [
            'referencedTable' => 'test_values',
            'referencedColumns' => 'id',
        ]);

        $this->assertTrue(
            $tableForge->hasForeignKey('value_id')
        );
    }

    public function testHasForeignKeyFalse(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'int',
        ]);

        $this->assertFalse(
            $tableForge->hasForeignKey('value_id')
        );
    }

    public function testHasIndex(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addIndex('id');

        $this->assertTrue(
            $tableForge->hasIndex('id')
        );
    }

    public function testHasIndexFalse(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $this->assertFalse(
            $tableForge->hasIndex('id')
        );
    }

    public function testIndex(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addIndex('id');

        $this->assertSame(
            [
                'columns' => [
                    'id',
                ],
                'unique' => false,
                'primary' => false,
                'type' => 'btree',
            ],
            $tableForge->index('id')
        );
    }

    public function testIndexes(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'int',
        ]);

        $tableForge->addIndex('id');

        $this->assertSame(
            [
                'id' => [
                    'columns' => [
                        'id',
                    ],
                    'unique' => false,
                    'primary' => false,
                    'type' => 'btree',
                ],
            ],
            $tableForge->indexes()
        );
    }
}
