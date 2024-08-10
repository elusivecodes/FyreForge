<?php
declare(strict_types=1);

namespace Tests\Sqlite\TableForge;

trait DiffDefaultsTestTrait
{
    public function testTableDiffDefaultsBigInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'bigint',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'bigint',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsChar(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'char',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'char',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsDate(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'date',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'date',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsDatetime(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'datetime',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'datetime',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsDecimal(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'decimal',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'decimal',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsFloat(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'float',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'float',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'int',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'int',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsMediumInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'mediumint',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'mediumint',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsSmallInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'smallint',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'smallint',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsTime(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'time',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'time',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsTimestamp(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'timestamp',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'timestamp',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsTinyInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'tinyint',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'tinyint',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsVarchar(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'varchar',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'varchar',
                ])
                ->sql()
        );
    }
}
