<?php
declare(strict_types=1);

namespace Tests\Forge;

use
    Fyre\Forge\ForgeRegistry,
    PHPUnit\Framework\TestCase,
    Tests\ConnectionTrait;

final class ForgeTest extends TestCase
{

    use
        AddColumnTest,
        AddForeignKeyTest,
        AddIndexTest,
        AlterTableTest,
        ChangeColumnTest,
        CreateSchemaTest,
        CreateTableTest,
        ConnectionTrait,
        DropColumnTest,
        DropForeignKeyTest,
        DropIndexTest,
        DropSchemaTest,
        DropTableTest,
        RenameTableTest;

}
