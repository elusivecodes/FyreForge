<?php
declare(strict_types=1);

namespace Tests\Forge;

trait AddIndexTestTrait
{

    public function testAddIndex(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ],
            'value' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->addIndex('test', 'id_value', [
            'columns' => ['id', 'value'],
            'unique' => true
        ]);

        $this->assertTrue(
            $this->schema->describe('test')
                ->hasIndex('id_value')
        );
    }

    public function testAddIndexSql(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD INDEX value (value) USING BTREE',
            $this->forge->addIndexSql('test', 'value')
        );
    }

    public function testAddIndexSqlColumns(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD INDEX name (value) USING BTREE',
            $this->forge->addIndexSql('test', 'name', [
                'columns' => 'value'
            ])
        );
    }

    public function testAddIndexSqlMultipleColumns(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD INDEX name (value, test) USING BTREE',
            $this->forge->addIndexSql('test', 'name', [
                'columns' => ['value', 'test']
            ])
        );
    }

    public function testAddIndexSqlPrimary(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD PRIMARY KEY (id)',
            $this->forge->addIndexSql('test', 'PRIMARY', [
                'columns' => [
                    'id'
                ]
            ])
        );
    }

    public function testAddIndexSqlFulltext(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD FULLTEXT INDEX value (value)',
            $this->forge->addIndexSql('test', 'value', [
                'type' => 'fulltext'
            ])
        );
    }

    public function testAddIndexSqlUnique(): void
    {
        $this->assertSame(
            'ALTER TABLE test ADD UNIQUE KEY value (value) USING BTREE',
            $this->forge->addIndexSql('test', 'value', [
                'unique' => true
            ])
        );
    }

}
