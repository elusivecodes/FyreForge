<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\Index;

/**
 * PostgresIndex
 */
class PostgresIndex extends Index
{
    /**
     * New PostgresIndex constructor.
     *
     * @param PostgresTable $table The Table.
     * @param string $name The index name.
     * @param array|string $columns The index columns.
     * @param bool $unique Whether the index is unique.
     * @param bool $primary Whether the index is primary.
     * @param string|null $type The index type.
     */
    public function __construct(
        PostgresTable $table,
        string $name,
        array|string $columns,
        bool $unique = false,
        bool $primary = false,
        string $type = 'btree',
    ) {
        parent::__construct($table, $name, $columns, $unique, $primary, $type);
    }
}
