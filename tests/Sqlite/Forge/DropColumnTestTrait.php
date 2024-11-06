<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

use Fyre\DB\Exceptions\DbException;
use Fyre\DB\Types\IntegerType;

trait DropColumnTestTrait
{
    public function testDropColumn(): void
    {
        // not supported with sqlite version bundled with PHP
        $this->expectException(DbException::class);

        $this->forge->createTable('test', [
            'id' => [
                'type' => IntegerType::class,
            ],
            'value' => [
                'type' => IntegerType::class,
            ],
        ]);

        $this->forge->dropColumn('test', 'value');

        $this->assertFalse(
            $this->schema->describe('test')
                ->hasColumn('value')
        );
    }
}
