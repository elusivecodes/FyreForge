<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\MySQL;

use
    Fyre\Forge\TableForgeInterface,
    Fyre\Forge\Traits\TableForgeTrait,
    Fyre\Schema\Handlers\MySQL\MySQLTableSchema;

/**
 * MySQLTableTable
 */
class MySQLTableForge extends MySQLTableSchema implements TableForgeInterface
{

    use
        TableForgeTrait;

}
