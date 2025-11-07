<?php
declare(strict_types=1);

namespace Tests\Mysql\Forge;

use PHPUnit\Framework\TestCase;
use Tests\Mysql\MysqlConnectionTrait;

final class ForgeTest extends TestCase
{
    use AddColumnTestTrait;
    use AddForeignKeyTestTrait;
    use AddIndexTestTrait;
    use AlterTableTestTrait;
    use ChangeColumnTestTrait;
    use CreateSchemaTestTrait;
    use CreateTableTestTrait;
    use DropColumnTestTrait;
    use DropForeignKeyTestTrait;
    use DropIndexTestTrait;
    use DropSchemaTestTrait;
    use DropTableTestTrait;
    use MergeQueryTestTrait;
    use MysqlConnectionTrait;
    use RenameColumnTestTrait;
    use RenameTableTestTrait;

    public function testDebug(): void
    {
        $data = $this->forge->__debugInfo();

        $this->assertSame(
            [],
            $data
        );
    }
}
