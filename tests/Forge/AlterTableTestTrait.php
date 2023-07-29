<?php
declare(strict_types=1);

namespace Tests\Forge;

trait AlterTableTestTrait
{

    public function testAlterTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->forge->alterTable('test', [
            'engine' => 'MyISAM'
        ]);

        $this->assertSame(
            [
                'engine' => 'MyISAM',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'comment' => ''
            ],
            $this->schema->table('test')
        );
    }
    
    public function testAlterTableSqlEngine(): void
    {
        $this->assertSame(
            'ALTER TABLE test ENGINE = InnoDB',
            $this->forge->alterTableSql('test', [
                'engine' => 'InnoDB'
            ])
        );
    }

    public function testAlterTableSqlCharsetCollation(): void
    {
        $this->assertSame(
            'ALTER TABLE test DEFAULT CHARSET = \'utf8\' COLLATE = \'utf8_unicode_ci\'',
            $this->forge->alterTableSql('test', [
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
        );
    }

}
