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
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\ForgeQueryGenerator;

use function implode;
use function str_starts_with;
use function strtoupper;

/**
 * SqliteForgeQueryGenerator
 */
class SqliteForgeQueryGenerator extends ForgeQueryGenerator
{
    /**
     * Generate SQL for adding a constraint.
     *
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     */
    public function buildAddConstraint(string $index, array $options)
    {
        $sql = 'ADD CONSTRAINT ';
        $sql .= $this->buildConstraint($index, $options);

        return $sql;
    }

    /**
     * Generate SQL for a column.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildColumn(string $column, array $options): string
    {
        $options = $this->parseColumnOptions($options);

        $sql = $column;

        if ($options['unsigned']) {
            $sql .= ' UNSIGNED';
        }

        $sql .= ' ';
        $sql .= strtoupper($options['type']);

        if ($options['length'] !== null) {
            switch ($options['type']) {
                case 'bit':
                case 'char':
                case 'varchar':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'integer':
                case 'bigint':
                    $sql .= '(';
                    $sql .= $options['length'];
                    $sql .= ')';
                    break;
                case 'decimal':
                case 'numeric':
                    $sql .= '(';
                    $sql .= $options['length'];
                    $sql .= ',';
                    $sql .= $options['precision'];
                    $sql .= ')';
                    break;
            }
        }

        if ($options['nullable']) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($options['default'] !== null) {
            $sql .= ' DEFAULT ';
            if (str_starts_with($options['default'], 'current_timestamp')) {
                $sql .= strtoupper($options['default']);
            } else {
                $sql .= $options['default'];
            }
        }

        return $sql;
    }

    /**
     * Generate SQL for a constraint.
     *
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     *
     * @throws ForgeException if the constraint is not valid.
     */
    public function buildConstraint(string $index, array $options): string
    {
        $options = $this->parseIndexOptions($options, $index);

        if ($options['primary']) {
            $sql = 'PRIMARY';
        } else if ($options['unique']) {
            $sql = $index;
            $sql .= ' UNIQUE';
        } else {
            throw ForgeException::forInvalidConstraint($index);
        }

        $sql .= ' KEY (';
        $sql .= implode(', ', $options['columns']);
        $sql .= ')';

        return $sql;
    }

    /**
     * Generate SQL for creating a table index.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The column options.
     * @return string The SQL query.
     *
     * @throws ForgeException if the index is not valid.
     */
    public function buildCreateIndex(string $table, string $index, array $options): string
    {
        $options = $this->parseIndexOptions($options, $index);

        if ($options['primary']) {
            throw new ForgeException('Constraints cannot be added to SQLite tables: '.$index);
        }

        $sql = 'CREATE ';

        if ($options['unique']) {
            $sql .= 'UNIQUE ';
        }

        $sql .= 'INDEX ';
        $sql .= $index;
        $sql .= ' ON ';
        $sql .= $table;
        $sql .= ' (';
        $sql .= implode(', ', $options['columns']);
        $sql .= ')';

        return $sql;
    }

    /**
     * Generate SQL for creating a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     *
     * @throws ForgeException if non-constraint indexes are added.
     */
    public function buildCreateTable(string $table, array $columns, array $options = []): string
    {
        $options['indexes'] ??= [];
        $options['foreignKeys'] ??= [];
        $options['ifNotExists'] ??= false;

        $definitions = array_map(
            fn(string $column, array $options) => $this->buildColumn($column, $options),
            array_keys($columns),
            $columns
        );

        foreach ($options['indexes'] as $index => $indexOptions) {
            if (is_numeric($index)) {
                $index = $indexOptions;
                $indexOptions = [];
            }

            $indexOptions = $this->parseIndexOptions($indexOptions, $index);

            if ($indexOptions['primary']) {
                $definitions[] = $this->buildConstraint($index, $indexOptions);
            } else {
                throw ForgeException::forInvalidIndexOnTableCreation($index);
            }
        }

        foreach ($options['foreignKeys'] as $foreignKey => $foreignKeyOptions) {
            $definitions[] = $this->buildForeignKey($foreignKey, $foreignKeyOptions);
        }

        $sql = 'CREATE TABLE ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $table;

        $sql .= ' (';
        $sql .= implode(', ', $definitions);
        $sql .= ')';

        return $sql;
    }

    /**
     * Parse column options.
     *
     * @param array $options The column options.
     * @return array The parsed options.
     *
     * @throws ForgeException if the column type is not supported by the connection handler.
     */
    public function parseColumnOptions(array $options = []): array
    {
        $options = parent::parseColumnOptions($options);

        $options['unsigned'] ??= false;

        switch ($options['type']) {
            case BinaryType::class:
                $options['type'] = 'blob';
                break;
            case BooleanType::class:
                $options['type'] = 'boolean';
                break;
            case DateTimeFractionalType::class:
                $options['type'] = 'datetimefractional';
                break;
            case DateTimeTimeZoneType::class:
                $options['type'] = 'timestamptimezone';
                break;
            case DateTimeType::class:
                $options['type'] = 'datetime';
                break;
            case DateType::class:
                $options['type'] = 'date';
                break;
            case DecimalType::class:
                $options['type'] = 'numeric';
                break;
            case FloatType::class:
                $options['type'] = 'real';
                break;
            case IntegerType::class:
                $options['length'] ??= 10;

                if ($options['length'] <= ($options['unsigned'] ? 3 : 4)) {
                    $options['type'] = 'tinyint';
                } else if ($options['length'] <= ($options['unsigned'] ? 5 : 6)) {
                    $options['type'] = 'smallint';
                } else if ($options['length'] <= ($options['unsigned'] ? 7 : 8)) {
                    $options['type'] = 'mediumint';
                } else if ($options['length'] <= ($options['unsigned'] ? 8 : 9)) {
                    $options['type'] = 'int';
                } else if ($options['length'] <= ($options['unsigned'] ? 10 : 11)) {
                    $options['type'] = 'integer';
                } else {
                    $options['type'] = 'bigint';
                }
                break;
            case JsonType::class:
                $options['type'] = 'json';
                break;
            case StringType::class:
                $options['length'] ??= 80;

                $options['type'] = $options['length'] === 1 ?
                    'char' :
                    'varchar';
                break;
            case TextType::class:
                $options['type'] = 'text';
                break;
            case TimeType::class:
                $options['type'] = 'time';
                break;
            case EnumType::class:
            case SetType::class:
                throw ForgeException::forUnsupportedColumnType($options['type']);
            default:
                $options['type'] = strtolower($options['type']);
                break;
        }

        switch ($options['type']) {
            case 'char':
                $options['length'] ??= 1;
                break;
            case 'tinyint':
                $options['length'] ??= $options['unsigned'] ? 3 : 4;
                break;
            case 'smallint':
                $options['length'] ??= $options['unsigned'] ? 5 : 6;
                break;
            case 'mediumint':
                $options['length'] ??= $options['unsigned'] ? 7 : 8;
                break;
            case 'decimal':
            case 'int':
            case 'numeric':
                $options['length'] ??= $options['unsigned'] ? 10 : 11;
                break;
            case 'bigint':
                $options['length'] ??= $options['unsigned'] ? 19 : 20;
                break;
            case 'varchar':
                $options['length'] ??= 80;
                break;
            default:
                $options['length'] = null;
                break;
        }

        switch ($options['type']) {
            case 'decimal':
            case 'numeric':
                $options['precision'] ??= 0;
                break;
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'integer':
            case 'bigint':
                $options['precision'] = 0;
                break;
            default:
                $options['precision'] = null;
                break;
        }

        switch ($options['type']) {
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
                $options['unsigned'] = false;
                break;
        }

        $options['unsigned'] = (bool) $options['unsigned'];

        return $options;
    }
}
