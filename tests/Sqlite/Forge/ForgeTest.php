<?php
declare(strict_types=1);

namespace Tests\Sqlite\Forge;

use PHPUnit\Framework\TestCase;
use Tests\Sqlite\SqliteConnectionTrait;

final class ForgeTest extends TestCase
{
    use AddColumnTestTrait;
    use AddIndexTestTrait;
    use CreateTableTestTrait;
    use DropColumnTestTrait;
    use DropIndexTestTrait;
    use DropTableTestTrait;
    use MergeQueryTestTrait;
    use RenameColumnTestTrait;
    use RenameTableTestTrait;
    use SqliteConnectionTrait;

    public function testDebug(): void
    {
        $data = $this->forge->__debugInfo();

        $this->assertSame(
            [],
            $data
        );
    }
}
