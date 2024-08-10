<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

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

    public function testTableDiffDefaultsBytea(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'bytea',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'bytea',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsCharacter(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'character',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'character',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsDoublePrecision(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'double precision',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'double precision',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsInteger(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'integer',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'integer',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsNumeric(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'numeric',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'numeric',
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsReal(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'real',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'real',
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

    public function testTableDiffDefaultsText(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => 'text',
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => 'text',
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
}
