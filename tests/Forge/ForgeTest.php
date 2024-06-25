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
    use ConnectionTrait;
    use CreateSchemaTestTrait;
    use CreateTableTestTrait;
    use DropColumnTestTrait;
    use DropForeignKeyTestTrait;
    use DropIndexTestTrait;
    use DropSchemaTestTrait;
    use DropTableTestTrait;
    use MergeQueryTestTrait;
    use RenameTableTestTrait;
}
