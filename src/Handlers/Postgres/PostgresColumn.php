<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

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

use function str_starts_with;
use function strtolower;
use function strtoupper;

/**
 * PostgresColumn
 */
class PostgresColumn extends Column
{
    protected static array $typeAliases = [
        'char' => 'character',
        'varchar' => 'character varying',
        'double' => 'double precision',
        'int' => 'integer',
        'time' => 'time without time zone',
        'timestamptz' => 'timestamp with time zone',
        'timestamp' => 'timestamp without time zone',
    ];

    /**
     * New PostgresColumn constructor.
     *
     * @param PostgresTable $table The Table.
     * @param string $name The column name.
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @param bool $nullable Whether the column is nullable.
     * @param string|null $default The column default value.
     * @param string|null $comment The column comment.
     * @param bool $autoIncrement Whether the column is auto-incrementing.
     */
    public function __construct(
        PostgresTable $table,
        string $name,
        string $type = StringType::class,
        int|null $length = null,
        int|null $precision = null,
        bool $nullable = false,
        string|null $default = null,
        string|null $comment = '',
        bool $autoIncrement = false,
    ) {
        parent::__construct(
            $table,
            $name,
            $type,
            $length,
            $precision,
            $nullable,
            false,
            $default,
            $comment,
            $autoIncrement
        );

        switch ($this->type) {
            case BinaryType::class:
                $this->type = 'bytea';
                break;
            case BooleanType::class:
                $this->type = 'boolean';
                break;
            case DateTimeFractionalType::class:
                $this->type = 'timestamp without time zone';
                break;
            case DateTimeTimeZoneType::class:
                $this->type = 'timestamp with time zone';
                break;
            case DateTimeType::class:
                $this->type = 'timestamp without time zone';
                $this->precision = 0;
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

                if ($this->length <= 6) {
                    $this->type = 'smallint';
                } else if ($this->length <= 8) {
                    $this->type = 'mediumint';
                } else if ($this->length <= 11) {
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
                    'character' :
                    'character varying';
                break;
            case TextType::class:
                $this->type = 'text';
                break;
            case TimeType::class:
                $this->type = 'time without time zone';
                break;
            case EnumType::class:
            case SetType::class:
                throw ForgeException::forUnsupportedColumnType($this->type);
            default:
                $this->type = strtolower($this->type);
                break;
        }

        $type = $this->type;

        $this->type = static::$typeAliases[$type] ?? $type;

        if ($this->default !== null) {
            $this->default = (string) $this->default;
            $default = strtoupper($this->default);
            if (str_starts_with($default, 'CURRENT_TIMESTAMP') || $default === 'NULL') {
                $this->default = $default;
            }
        }

        switch ($this->type) {
            case 'bit':
            case 'character':
                $this->length ??= 1;
                break;
            case 'smallint':
            case 'smallserial':
                $this->length = 6;
                break;
            case 'integer':
            case 'serial':
                $this->length = 11;
                break;
            case 'bigint':
            case 'bigserial':
                $this->length = 20;
                break;
            case 'numeric':
                $this->length ??= 10;
                break;
            case 'character varying':
                $this->length ??= 80;
                break;
            case 'bit varying':
                break;
            default:
                $this->length = null;
                break;
        }

        switch ($this->type) {
            case 'numeric':
                $this->precision ??= 0;
                break;
            case 'date':
            case 'smallint':
            case 'smallserial':
            case 'integer':
            case 'serial':
            case 'bigint':
            case 'bigserial':
                $this->precision = 0;
                break;
            case 'time without time zone':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                $this->precision ??= 6;
                break;
            default:
                $this->precision = null;
                break;
        }
    }
}
