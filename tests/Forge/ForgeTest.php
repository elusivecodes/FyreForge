<?php
declare(strict_types=1);

namespace Tests\Forge;

use PHPUnit\Framework\TestCase;
use Tests\ConnectionTrait;

final class ForgeTest extends TestCase
{

    use AddColumnTestTrait;
    use AddForeignKeyTestTrait;
    use AddIndexTestTrait;
    use AlterTableTestTrait;
    use ChangeColumnTestTrait;
    use CreateSchemaTestTrait;
    use CreateTableTestTrait;
    use ConnectionTrait;
    use DropColumnTestTrait;
    use DropForeignKeyTestTrait;
    use DropIndexTestTrait;
    use DropSchemaTestTrait;
    use DropTableTestTrait;
    use RenameTableTestTrait;

}
