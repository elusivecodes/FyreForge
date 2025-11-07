<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Schema\Index as SchemaIndex;
use Fyre\Utility\Traits\MacroTrait;

use function get_object_vars;
use function strtolower;

/**
 * Index
 */
class Index
{
    use MacroTrait;

    protected array $columns;

    /**
     * New Index constructor.
     *
     * @param Table $table The Table.
     * @param string $name The index name.
     * @param array|string $columns The index columns.
     * @param bool $unique Whether the index is unique.
     * @param bool $primary Whether the index is primary.
     * @param string|null $type The index type.
     */
    public function __construct(
        protected Table $table,
        protected string $name,
        array|string $columns = [],
        protected bool $unique = false,
        protected bool $primary = false,
        protected string|null $type = null,
    ) {
        $this->columns = (array) $columns;

        if ($this->primary) {
            $this->unique = true;
        }

        if ($this->type) {
            $this->type = strtolower($this->type);
        }
    }

    /**
     * Get the debug info of the object.
     *
     * @return array The debug info.
     */
    public function __debugInfo(): array
    {
        $data = get_object_vars($this);

        unset($data['table']);

        return $data;
    }

    /**
     * Determine whether this index is equivalent to a Schema Index.
     *
     * @param SchemaIndex $schemaIndex The Schema Index.
     * @return bool TRUE if the indexes are equivalent, otherwise FALSE.
     */
    public function compare(SchemaIndex $schemaIndex): bool
    {
        return $this->columns === $schemaIndex->getColumns() &&
            $this->unique === $schemaIndex->isUnique() &&
            $this->primary === $schemaIndex->isPrimary() &&
            $this->type === $schemaIndex->getType();

    }

    /**
     * Get the index columns.
     *
     * @return array The index columns.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the index name.
     *
     * @return string The index name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Table.
     *
     * @return Table The Table.
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * Get the index type.
     *
     * @return string|null The index type.
     */
    public function getType(): string|null
    {
        return $this->type;
    }

    /**
     * Determine whether the index is primary.
     *
     * @return bool TRUE if the index is primary, otherwise FALSE.
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * Determine whether the index is unique.
     *
     * @return bool TRUE if the index is unique, otherwise FALSE.
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * Get the index data as an array.
     *
     * @return array The index data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'columns' => $this->columns,
            'unique' => $this->unique,
            'primary' => $this->primary,
            'type' => $this->type,
        ];
    }
}
