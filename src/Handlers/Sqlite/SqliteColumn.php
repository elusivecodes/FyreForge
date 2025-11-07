<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

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
use Fyre\Forge\Exceptions\ForgeException;

use function strtolower;

/**
 * SqliteColumn
 */
class SqliteColumn extends Column
{
    /**
     * New SqliteColumn constructor.
     *
     * @param SqliteTable $table The Table.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param bool $unsigned Whether the column is unsigned.
     * @param string|null $default The column default value.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     */
    public function __construct(
        SqliteTable $table,
        string $name,
        string $type = StringType::class,
        int|null $length = null,
        int|null $precision = null,
        bool $nullable = false,
        bool $unsigned = false,
        string|null $default = null,
        bool $autoIncrement = false,
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
            null,
            $autoIncrement
        );

        switch ($this->type) {
            case BinaryType::class:
                $this->type = 'blob';
                break;
            case BooleanType::class:
                $this->type = 'boolean';
                break;
            case DateTimeFractionalType::class:
                $this->type = 'datetimefractional';
                break;
            case DateTimeTimeZoneType::class:
                $this->type = 'timestamptimezone';
                break;
            case DateTimeType::class:
                $this->type = 'datetime';
                break;
            case DateType::class:
                $this->type = 'date';
                break;
            case DecimalType::class:
                $this->type = 'numeric';
                break;
            case FloatType::class:
                $this->type = 'real';
                break;
            case IntegerType::class:
                $this->length ??= 10;

                if ($this->length <= ($this->unsigned ? 3 : 4)) {
                    $this->type = 'tinyint';
                } else if ($this->length <= ($this->unsigned ? 5 : 6)) {
                    $this->type = 'smallint';
                } else if ($this->length <= ($this->unsigned ? 7 : 8)) {
                    $this->type = 'mediumint';
                } else if ($this->length <= ($this->unsigned ? 8 : 9)) {
                    $this->type = 'int';
                } else if ($this->length <= ($this->unsigned ? 10 : 11)) {
                    $this->type = 'integer';
                } else {
                    $this->type = 'bigint';
                }
                break;
            case JsonType::class:
                $this->type = 'json';
                break;
            case StringType::class:
                $this->length ??= 80;

                $this->type = $this->length === 1 ?
                    'char' :
                    'varchar';
                break;
            case TextType::class:
                $this->type = 'text';
                break;
            case TimeType::class:
                $this->type = 'time';
                break;
            case EnumType::class:
            case SetType::class:
                throw ForgeException::forUnsupportedColumnType($this->type);
            default:
                $this->type = strtolower($this->type);
                break;
        }

        switch ($this->type) {
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
            case 'numeric':
                $this->length ??= $this->unsigned ? 10 : 11;
                break;
            case 'bigint':
                $this->length ??= $this->unsigned ? 19 : 20;
                break;
            case 'varchar':
                $this->length ??= 80;
                break;
            default:
                $this->length = null;
                break;
        }

        switch ($this->type) {
            case 'decimal':
            case 'numeric':
                $this->precision ??= 0;
                break;
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'integer':
            case 'bigint':
                $this->precision = 0;
                break;
            default:
                $this->precision = null;
                break;
        }

        switch ($this->type) {
            case 'decimal':
            case 'numeric':
            case 'bit':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'integer':
            case 'bigint':
            case 'float':
            case 'real':
            case 'double':
                break;
            default:
                $this->unsigned = false;
                break;
        }
    }
}
