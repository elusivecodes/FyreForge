<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\DB\Types\BinaryType;
use Fyre\DB\Types\BooleanType;
use Fyre\DB\Types\DateTimeFractionalType;
use Fyre\DB\Types\DateTimeTimeZoneType;
use Fyre\DB\Types\DateTimeType;
use Fyre\DB\Types\DateType;
use Fyre\DB\Types\DecimalType;
use Fyre\DB\Types\EnumType;
use Fyre\DB\Types\FloatType;
use Fyre\DB\Types\IntegerType;
use Fyre\DB\Types\JsonType;
use Fyre\DB\Types\SetType;
use Fyre\DB\Types\StringType;
use Fyre\DB\Types\TextType;
use Fyre\DB\Types\TimeType;
use Fyre\Forge\Column;
use Fyre\Schema\Column as SchemaColumn;
use Override;

use function str_contains;
use function str_starts_with;
use function strtolower;

/**
 * MysqlColumn
 */
class MysqlColumn extends Column
{
    /**
     * New MysqlColumn constructor.
     *
     * @param MysqlTable $table The Table.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param bool $unsigned Whether the column is unsigned.
     * @param string|null $default The column default value.
     * @param string|null $comment The column comment.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     */
    public function __construct(
        MysqlTable $table,
        string $name,
        string $type = StringType::class,
        int|null $length = null,
        int|null $precision = null,
        bool $nullable = false,
        bool $unsigned = false,
        string|null $default = null,
        string $comment = '',
        bool $autoIncrement = false,
        protected array|null $values = null,
        protected string|null $charset = null,
        protected string|null $collation = null,
    ) {
        parent::__construct(
            $table,
            $name,
            $type,
            $length,
            $precision,
            $nullable,
            $unsigned,
            $default,
            $comment,
            $autoIncrement
        );

        switch ($this->type) {
            case BinaryType::class:
                $this->length ??= 65535;

                if ($this->length <= 255) {
                    $this->type = 'tinyblob';
                } else if ($this->length <= 65535) {
                    $this->type = 'blob';
                } else if ($this->length <= 16777215) {
                    $this->type = 'mediumblob';
                } else {
                    $this->type = 'longblob';
                }
                break;
            case BooleanType::class:
                $this->type = 'tinyint';
                $this->length = 1;
                break;
            case DateTimeFractionalType::class:
            case DateTimeTimeZoneType::class:
            case DateTimeType::class:
                $this->type = 'datetime';
                break;
            case DateType::class:
                $this->type = 'date';
                break;
            case DecimalType::class:
                $this->type = 'decimal';
                break;
            case EnumType::class:
                $this->type = 'enum';
                break;
            case FloatType::class:
                $this->type = 'float';
                break;
            case IntegerType::class:
                $this->unsigned ??= false;
                $this->length ??= $this->unsigned ? 10 : 11;

                if ($this->length <= ($this->unsigned ? 3 : 4)) {
                    $this->type = 'tinyint';
                } else if ($this->length <= ($this->unsigned ? 5 : 6)) {
                    $this->type = 'smallint';
                } else if ($this->length <= ($this->unsigned ? 7 : 8)) {
                    $this->type = 'mediumint';
                } else if ($this->length <= ($this->unsigned ? 10 : 11)) {
                    $this->type = 'int';
                } else {
                    $this->type = 'bigint';
                }
                break;
            case JsonType::class:
                $this->type = 'json';
                break;
            case SetType::class:
                $this->type = 'set';
                break;
            case StringType::class:
                $this->length ??= 80;

                $this->type = $this->length === 1 ?
                    'char' :
                    'varchar';
                break;
            case TextType::class:
                $this->length ??= 65535;

                if ($this->length <= 255) {
                    $this->type = 'tinytext';
                } else if ($this->length <= 65535) {
                    $this->type = 'text';
                } else if ($this->length <= 16777215) {
                    $this->type = 'mediumtext';
                } else {
                    $this->type = 'longtext';
                }
                break;
            case TimeType::class:
                $this->type = 'time';
                break;
            default:
                $this->type = strtolower($this->type);
                break;
        }

        if ($this->type === 'json' && str_contains($this->table->getForge()->getConnection()->version(), 'MariaDB')) {
            $this->type = 'longtext';
            $this->charset = 'utf8mb4';
            $this->collation = 'utf8mb4_bin';
        }

        if ($this->default !== null) {
            $this->default = (string) $this->default;
            $default = strtolower($this->default);
            if ($default === 'current_timestamp') {
                $this->default = 'current_timestamp()';
            } else if (str_starts_with($default, 'current_timestamp')) {
                $this->default = $default;
            } else if ($default === 'null') {
                $this->default = 'NULL';
            }
        } else if ($this->type === 'timestamp') {
            $this->default = 'current_timestamp()';
        }

        switch ($this->type) {
            case 'char':
            case 'varchar':
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'enum':
            case 'set':
                $this->charset ??= $this->table->getCharset();
                $this->collation ??= $this->table->getCollation();
                break;
            default:
                $this->charset = null;
                $this->collation = null;
                break;
        }

        switch ($this->type) {
            case 'bit':
            case 'char':
                $this->length ??= 1;
                break;
            case 'tinyint':
                $this->length ??= $this->unsigned ? 3 : 4;
                break;
            case 'smallint':
                $this->length ??= $this->unsigned ? 5 : 6;
                break;
            case 'mediumint':
                $this->length ??= $this->unsigned ? 7 : 8;
                break;
            case 'decimal':
            case 'int':
                $this->length ??= $this->unsigned ? 10 : 11;
                break;
            case 'bigint':
                $this->length ??= $this->unsigned ? 19 : 20;
                break;
            case 'varchar':
                $this->length ??= 80;
                break;
            case 'tinyblob':
            case 'tinytext':
                $this->length = 255;
                break;
            case 'blob':
            case 'text':
                $this->length = 65535;
                break;
            case 'mediumblob':
            case 'mediumtext':
                $this->length = 16777215;
                break;
            case 'longblob':
            case 'longtext':
                $this->length = 4294967295;
                break;
            case 'binary':
            case 'varbinary':
                break;
            default:
                $this->length = null;
                break;
        }

        switch ($this->type) {
            case 'decimal':
                $this->precision ??= 0;
                break;
            case 'bit':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                $this->precision = 0;
                break;
            default:
                $this->precision = null;
                break;
        }

        switch ($this->type) {
            case 'enum':
            case 'set':
                $this->values ??= [];
                break;
            default:
                $this->values = null;
                break;
        }

        switch ($this->type) {
            case 'decimal':
            case 'bit':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'float':
            case 'double':
                break;
            default:
                $this->unsigned = false;
                break;
        }
    }

    /**
     * Determine whether this column is equivalent to a Schema Column.
     *
     * @param SchemaColumn $schemaColumn The Schema Column.
     * @return bool TRUE if the columns are equivalent, otherwise FALSE.
     */
    #[Override]
    public function compare(SchemaColumn $schemaColumn): bool
    {
        return parent::compare($schemaColumn) &&
            $this->values === $schemaColumn->getValues() &&
            $this->charset === $schemaColumn->getCharset() &&
            $this->collation === $schemaColumn->getCollation();
    }

    /**
     * Get the column character set.
     *
     * @return string|null The column character set.
     */
    public function getCharset(): string|null
    {
        return $this->charset;
    }

    /**
     * Get the column collation.
     *
     * @return string|null The column collation.
     */
    public function getCollation(): string|null
    {
        return $this->collation;
    }

    /**
     * Get the column enum values.
     *
     * @return array|null The column enum values.
     */
    public function getValues(): array|null
    {
        return $this->values;
    }

    /**
     * Get the column data as an array.
     *
     * @return array The column data.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'precision' => $this->precision,
            'values' => $this->values,
            'nullable' => $this->nullable,
            'unsigned' => $this->unsigned,
            'default' => $this->default,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'comment' => $this->comment,
            'autoIncrement' => $this->autoIncrement,
        ];
    }
}
