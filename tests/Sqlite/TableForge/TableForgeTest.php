<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

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
    use RenameTestTrait;
    use SqliteConnectionTrait;

    public function testColumn(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value', [
            'type' => 'varchar',
        ]);

        $this->assertSame(
            [
                'type' => 'integer',
                'length' => null,
                'precision' => 0,
                'nullable' => false,
                'default' => null,
                'autoIncrement' => false,
                'unsigned' => false,
            ],
            $tableForge->column('id')
        );
    }

    public function testColumnNames(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
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
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value', [
            'type' => 'varchar',
        ]);

        $this->assertSame(
            [
                'id' => [
                    'type' => 'integer',
                    'length' => null,
                    'precision' => 0,
                    'nullable' => false,
                    'default' => null,
                    'autoIncrement' => false,
                    'unsigned' => false,
                ],
                'value' => [
                    'type' => 'varchar',
                    'length' => 80,
                    'precision' => null,
                    'nullable' => false,
                    'default' => null,
                    'autoIncrement' => false,
                    'unsigned' => false,
                ],
            ],
            $tableForge->columns()
        );
    }

    public function testForeignKey(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'integer',
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
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'integer',
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
            'type' => 'integer',
        ]);

        $this->assertTrue(
            $tableForge->hasColumn('id')
        );
    }

    public function testHasColumnFalse(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
        ]);

        $this->assertFalse(
            $tableForge->hasColumn('invalid')
        );
    }

    public function testHasForeignKey(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'integer',
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
            'type' => 'integer',
        ]);

        $tableForge->addColumn('value_id', [
            'type' => 'integer',
        ]);

        $this->assertFalse(
            $tableForge->hasForeignKey('value_id')
        );
    }

    public function testHasIndex(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
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
            'type' => 'integer',
        ]);

        $this->assertFalse(
            $tableForge->hasIndex('id')
        );
    }

    public function testIndex(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
        ]);

        $tableForge->addIndex('id');

        $this->assertSame(
            [
                'columns' => [
                    'id',
                ],
                'unique' => false,
                'primary' => false,
            ],
            $tableForge->index('id')
        );
    }

    public function testIndexes(): void
    {
        $tableForge = $this->forge->build('test');

        $tableForge->addColumn('id', [
            'type' => 'integer',
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
                ],
            ],
            $tableForge->indexes()
        );
    }
}
