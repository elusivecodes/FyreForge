<?php
declare(strict_types=1);

namespace Tests\TableForge;

trait TableTestTrait
{

    public function testOptionsNewTable(): void
    {
        $this->assertSame(
            [
                'CREATE TABLE test (id INT(11) NOT NULL) ENGINE = MyISAM DEFAULT CHARSET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\''
            ],
            $this->forge
                ->build('test', [
                    'engine' => 'MyISAM'
                ])
                ->addColumn('id', [
                    'type' => 'int'
                ])
                ->sql()
        );
    }

    public function testOptionsExistingTable(): void
    {
        $this->forge->createTable('test', [
            'id' => [
                'type' => 'int'
            ]
        ]);

        $this->assertSame(
            [
                'ALTER TABLE test ENGINE = MyISAM'
            ],
            $this->forge
                ->build('test', [
                    'engine' => 'MyISAM'
                ])
                ->sql()
        );
    }

}
