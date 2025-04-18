<?php
declare(strict_types=1);

namespace Tests\Postgres\TableForge;

use Fyre\DB\Types\BinaryType;
use Fyre\DB\Types\BooleanType;
use Fyre\DB\Types\DateTimeFractionalType;
use Fyre\DB\Types\DateType;
use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\FloatType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\StringType;
use Fyre\DB\Types\TextType;
use Fyre\DB\Types\TimeType;

trait DiffDefaultsTestTrait
{
    public function testTableDiffDefaultsBigInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => IntegerType::class,
                'length' => 20,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => IntegerType::class,
                    'length' => 20,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsBoolean(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => BooleanType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => BooleanType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsBytea(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => BinaryType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => BinaryType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsCharacter(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => StringType::class,
                'length' => 1,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => StringType::class,
                    'length' => 1,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsCharacterVarying(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => StringType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => StringType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsDate(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => DateType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => DateType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsInteger(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => IntegerType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsNumeric(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => DecimalType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => DecimalType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsReal(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => FloatType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => FloatType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsSmallInt(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => IntegerType::class,
                'length' => 6,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => IntegerType::class,
                    'length' => 6,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsText(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => TextType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => TextType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsTime(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => TimeType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => TimeType::class,
                ])
                ->sql()
        );
    }

    public function testTableDiffDefaultsTimestamp(): void
    {
        $this->forge->createTable('test', [
            'value' => [
                'type' => DateTimeFractionalType::class,
            ],
        ]);

        $this->assertSame(
            [],
            $this->forge
                ->build('test')
                ->clear()
                ->addColumn('value', [
                    'type' => DateTimeFractionalType::class,
                ])
                ->sql()
        );
    }
}
