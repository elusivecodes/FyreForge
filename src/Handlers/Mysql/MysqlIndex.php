<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Forge\Index;

/**
 * MysqlIndex
 */
class MysqlIndex extends Index
{
    /**
     * New MysqlIndex constructor.
     *
     * @param MysqlTable $table The Table.
     * @param string $name The index name.
     * @param array|string $columns The index columns.
     * @param bool $unique Whether the index is unique.
     * @param bool $primary Whether the index is primary.
     * @param string|null $type The index type.
     */
    public function __construct(
        MysqlTable $table,
        string $name,
        array|string $columns,
        bool $unique = false,
        bool $primary = false,
        string $type = 'btree',
    ) {
        parent::__construct($table, $name, $columns, $unique, $primary || $name === 'PRIMARY', $type);
    }
}
