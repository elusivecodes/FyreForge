<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\DB\Types\StringType;
use Fyre\Schema\Column as SchemaColumn;
use Fyre\Utility\Traits\MacroTrait;

use function get_object_vars;

/**
 * Column
 */
abstract class Column
{
    use MacroTrait;

    /**
     * New Column constructor.
     *
     * @param Table $table The Table.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param bool $unsigned Whether the column is unsigned.
     * @param string|null $default The column default value.
     * @param string $comment The column comment.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     */
    public function __construct(
        protected Table $table,
        protected string $name,
        protected string $type = StringType::class,
        protected int|null $length = null,
        protected int|null $precision = null,
        protected bool $nullable = false,
        protected bool $unsigned = false,
        protected string|null $default = null,
        protected string|null $comment = null,
        protected bool $autoIncrement = false,
    ) {
        if ($this->nullable && $this->default === null) {
            $this->default = 'NULL';
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
     * Determine whether this column is equivalent to a Schema Column.
     *
     * @param SchemaColumn $schemaColumn The Schema Column.
     * @return bool TRUE if the columns are equivalent, otherwise FALSE.
     */
    public function compare(SchemaColumn $schemaColumn): bool
    {
        return $this->type === $schemaColumn->getType() &&
            $this->length === $schemaColumn->getLength() &&
            $this->precision === $schemaColumn->getPrecision() &&
            $this->nullable === $schemaColumn->isNullable() &&
            $this->unsigned === $schemaColumn->isUnsigned() &&
            $this->default === $schemaColumn->getDefault() &&
            $this->comment === $schemaColumn->getComment() &&
            $this->autoIncrement === $schemaColumn->isAutoIncrement();
    }

    /**
     * Get the column comment.
     *
     * @return string|null The column comment.
     */
    public function getComment(): string|null
    {
        return $this->comment;
    }

    /**
     * Get the column default value.
     *
     * @return string|null The column default value.
     */
    public function getDefault(): string|null
    {
        return $this->default;
    }

    /**
     * Get the column length.
     *
     * @return int|null The column length.
     */
    public function getLength(): int|null
    {
        return $this->length;
    }

    /**
     * Get the column name.
     *
     * @return string The column name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the column precision.
     *
     * @return int|null The column precision.
     */
    public function getPrecision(): int|null
    {
        return $this->precision;
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
     * Get the column type.
     *
     * @return string The column type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Determine whether the column is an auto increment column.
     *
     * @return bool TRUE if the column is an auto increment column, otherwise FALSE.
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * Determine whether the column is nullable.
     *
     * @return bool TRUE if the column is nullable, otherwise FALSE.
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Determine whether the column is unsigned.
     *
     * @return bool TRUE if the column is unsigned, otherwise FALSE.
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * Get the column data as an array.
     *
     * @return array The column data.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'precision' => $this->precision,
            'nullable' => $this->nullable,
            'unsigned' => $this->unsigned,
            'default' => $this->default,
            'comment' => $this->comment,
            'autoIncrement' => $this->autoIncrement,
        ];
    }
}
