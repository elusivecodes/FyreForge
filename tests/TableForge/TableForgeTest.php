<?php
declare(strict_types=1);

namespace Tests\TableForge;

use
    Fyre\Forge\ForgeRegistry,
    PHPUnit\Framework\TestCase,
    Tests\ConnectionTrait;

final class TableForgeTest extends TestCase
{

    use
        AddColumnTest,
        AddForeignKeyTest,
        AddIndexTest,
        ChangeColumnTest,
        ConnectionTrait,
        DiffTest,
        DropTest,
        DropColumnTest,
        DropForeignKeyTest,
        DropIndexTest,
        ExecuteTest,
        RenameTest,
        TableTest;

}
