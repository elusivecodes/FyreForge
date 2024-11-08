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
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\ForgeQueryGenerator;

use function array_keys;
use function array_map;
use function implode;
use function is_numeric;
use function ltrim;
use function str_starts_with;
use function strtolower;
use function strtoupper;

/**
 * MysqlForgeQueryGenerator
 */
class MysqlForgeQueryGenerator extends ForgeQueryGenerator
{
    /**
     * Generate SQL for adding a foreign key to a table.
     *
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    public function buildAddForeignKey(string $foreignKey, array $options = []): string
    {
        $sql = 'ADD ';
        $sql .= $this->buildForeignKey($foreignKey, $options);

        return $sql;
    }

    /**
     * Generate SQL for adding an index to a table.
     *
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     */
    public function buildAddIndex(string $index, array $options = []): string
    {
        $sql = 'ADD ';
        $sql .= $this->buildIndex($index, $options);

        return $sql;
    }

    /**
     * Generate SQL for changing a table column.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @param bool $forceComment Whether to force updating the comment.
     * @return string The SQL query.
     */
    public function buildChangeColumn(string $column, array $options, bool $forceComment = false): string
    {
        $sql = 'CHANGE COLUMN ';
        $sql .= $column;
        $sql .= ' ';
        $sql .= $this->buildColumn($options['name'] ?? $column, $options, $forceComment);

        return $sql;
    }

    /**
     * Generate SQL for a column.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @param bool $forceComment Whether to force updating the comment.
     * @return string The SQL query.
     */
    public function buildColumn(string $column, array $options, bool $forceComment = false): string
    {
        $options = $this->parseColumnOptions($options);

        $options['after'] ??= null;
        $options['first'] ??= false;

        $sql = $column;
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
                case 'bigint':
                    $sql .= '(';
                    $sql .= $options['length'];
                    $sql .= ')';
                    break;
                case 'decimal':
                    $sql .= '(';
                    $sql .= $options['length'];
                    $sql .= ',';
                    $sql .= $options['precision'];
                    $sql .= ')';
                    break;
            }
        } else if ($options['values'] !== null) {
            switch ($options['type']) {
                case 'enum':
                case 'set':
                    $options['values'] = array_map(
                        fn($value): string => $this->forge->getConnection()->quote((string) $value),
                        $options['values']
                    );

                    $sql .= '(';
                    $sql .= implode(',', $options['values']);
                    $sql .= ')';
                    break;
            }
        }

        if ($options['unsigned']) {
            $sql .= ' UNSIGNED';
        }

        if ($options['charset']) {
            $sql .= ' CHARACTER SET '.$this->forge->getConnection()->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE '.$this->forge->getConnection()->quote($options['collation']);
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

        if ($options['autoIncrement']) {
            $sql .= ' AUTO_INCREMENT';
        }

        if ($options['comment'] || $forceComment) {
            $sql .= ' COMMENT '.$this->forge->getConnection()->quote($options['comment']);
        }

        if ($options['after']) {
            $sql .= ' AFTER '.$options['after'];
        } else if ($options['first']) {
            $sql .= ' FIRST';
        }

        return $sql;
    }

    /**
     * Generate SQL for creating a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return string The SQL query.
     */
    public function buildCreateSchema(string $schema, array $options = []): string
    {
        $options['ifNotExists'] ??= false;
        $options['charset'] ??= $this->forge->getConnection()->getCharset();
        $options['collation'] ??= $this->forge->getConnection()->getCollation();

        $sql = 'CREATE SCHEMA ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $schema;

        if ($options['charset']) {
            $sql .= ' CHARACTER SET = '.$this->forge->getConnection()->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE = '.$this->forge->getConnection()->quote($options['collation']);
        }

        return $sql;
    }

    /**
     * Generate SQL for creating a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    public function buildCreateTable(string $table, array $columns, array $options = []): string
    {
        $options = $this->parseTableOptions($options);

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

            $definitions[] = $this->buildIndex($index, $indexOptions);
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

        $tableOptionSql = $this->buildTableOptions($options);

        if ($tableOptionSql) {
            $sql .= ' ';
            $sql .= $tableOptionSql;
        }

        return $sql;
    }

    /**
     * Generate SQL for dropping a foreign key from a table.
     *
     * @param string $foreignKey The foreign key name.
     * @return string The SQL query.
     */
    public function buildDropForeignKey(string $foreignKey): string
    {
        $sql = 'DROP FOREIGN KEY ';
        $sql .= $foreignKey;

        return $sql;
    }

    /**
     * Generate SQL for dropping an index from a table.
     *
     * @param string $index The index name.
     * @return string The SQL query.
     */
    public function buildDropIndex(string $index): string
    {
        $sql = 'DROP INDEX ';
        $sql .= $index;

        return $sql;
    }

    /**
     * Generate SQL for dropping a primary key from a table.
     *
     * @param string $index The index name.
     * @return string The SQL query.
     */
    public function buildDropPrimaryKey(): string
    {
        return 'DROP PRIMARY KEY';
    }

    /**
     * Generate SQL for dropping a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return string The SQL query.
     */
    public function buildDropSchema(string $schema, array $options = []): string
    {
        $options['ifExists'] ??= false;

        $sql = 'DROP SCHEMA ';

        if ($options['ifExists']) {
            $sql .= 'IF EXISTS ';
        }

        $sql .= $schema;

        return $sql;
    }

    /**
     * Generate SQL for an index.
     *
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     *
     * @throws ForgeException if primary key index type is not valid.
     */
    public function buildIndex(string $index, array $options): string
    {
        $options = $this->parseIndexOptions($options, $index);

        $columns = implode(', ', $options['columns']);

        if ($options['primary']) {
            if ($options['type'] !== 'btree') {
                throw ForgeException::forInvalidIndexType($options['type']);
            }

            return 'PRIMARY KEY ('.$columns.')';
        }

        $type = strtoupper($options['type']);

        if ($options['unique']) {
            return 'CONSTRAINT '.$index.' UNIQUE KEY ('.$columns.') USING '.$type;
        }

        switch ($type) {
            case 'FULLTEXT':
                return 'FULLTEXT INDEX '.$index.' ('.$columns.')';
            case 'SPATIAL':
                return 'SPATIAL INDEX '.$index.' ('.$columns.')';
            default:
                return 'INDEX '.$index.' ('.$columns.') USING '.$type;
        }
    }

    /**
     * Prepate table SQL.
     *
     * @param array $options The table options.
     * @param bool $forceComment Whether to force updating the comment.
     * @return string The SQL query.
     */
    public function buildTableOptions(array $options, bool $forceComment = false): string
    {
        $options['engine'] ??= false;
        $options['charset'] ??= false;
        $options['collation'] ??= false;
        $options['comment'] ??= '';

        $sql = '';

        if ($options['engine']) {
            $sql .= ' ENGINE = '.$options['engine'];
        }

        if ($options['charset']) {
            $sql .= ' DEFAULT CHARSET = '.$this->forge->getConnection()->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE = '.$this->forge->getConnection()->quote($options['collation']);
        }

        if ($options['comment'] || $forceComment) {
            $sql .= ' COMMENT '.$this->forge->getConnection()->quote($options['comment']);
        }

        return ltrim($sql);
    }

    /**
     * Parse column options.
     *
     * @param array $options The column options.
     * @return array The parsed options.
     */
    public function parseColumnOptions(array $options = []): array
    {
        $options = parent::parseColumnOptions($options);

        $options['unsigned'] ??= false;
        $options['charset'] ??= null;
        $options['collation'] ??= null;
        $options['comment'] ??= '';

        switch ($options['type']) {
            case BinaryType::class:
                $options['length'] ??= 65535;

                if ($options['length'] <= 255) {
                    $options['type'] = 'tinyblob';
                } else if ($options['length'] <= 65535) {
                    $options['type'] = 'blob';
                } else if ($options['length'] <= 16777215) {
                    $options['type'] = 'mediumblob';
                } else {
                    $options['type'] = 'longblob';
                }
                break;
            case BooleanType::class:
                $options['type'] = 'tinyint';
                $options['length'] = 1;
                break;
            case DateTimeFractionalType::class:
            case DateTimeTimeZoneType::class:
            case DateTimeType::class:
                $options['type'] = 'datetime';
                break;
            case DateType::class:
                $options['type'] = 'date';
                break;
            case DecimalType::class:
                $options['type'] = 'decimal';
                break;
            case EnumType::class:
                $options['type'] = 'enum';
                break;
            case FloatType::class:
                $options['type'] = 'float';
                break;
            case IntegerType::class:
                $options['unsigned'] ??= false;
                $options['length'] ??= $options['unsigned'] ? 10 : 11;

                if ($options['length'] <= ($options['unsigned'] ? 3 : 4)) {
                    $options['type'] = 'tinyint';
                } else if ($options['length'] <= ($options['unsigned'] ? 5 : 6)) {
                    $options['type'] = 'smallint';
                } else if ($options['length'] <= ($options['unsigned'] ? 7 : 8)) {
                    $options['type'] = 'mediumint';
                } else if ($options['length'] <= ($options['unsigned'] ? 10 : 11)) {
                    $options['type'] = 'int';
                } else {
                    $options['type'] = 'bigint';
                }
                break;
            case JsonType::class:
                $options['type'] = 'json';
                break;
            case SetType::class:
                $options['type'] = 'set';
                break;
            case StringType::class:
                $options['length'] ??= 80;

                $options['type'] = $options['length'] === 1 ?
                    'char' :
                    'varchar';
                break;
            case TextType::class:
                $options['length'] ??= 65535;

                if ($options['length'] <= 255) {
                    $options['type'] = 'tinytext';
                } else if ($options['length'] <= 65535) {
                    $options['type'] = 'text';
                } else if ($options['length'] <= 16777215) {
                    $options['type'] = 'mediumtext';
                } else {
                    $options['type'] = 'longtext';
                }
                break;
            case TimeType::class:
                $options['type'] = 'time';
                break;
            default:
                $options['type'] = strtolower($options['type']);
                break;
        }

        if ($options['default'] !== null) {
            $options['default'] = (string) $options['default'];
            $default = strtolower($options['default']);
            if ($default === 'current_timestamp') {
                $options['default'] = 'current_timestamp()';
            } else if (str_starts_with($default, 'current_timestamp')) {
                $options['default'] = $default;
            } else if ($default === 'null') {
                $options['default'] = 'NULL';
            }
        } else if ($options['type'] === 'timestamp') {
            $options['default'] = 'current_timestamp()';
        }

        switch ($options['type']) {
            case 'char':
            case 'varchar':
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            case 'enum':
            case 'set':
                $options['charset'] ??= $this->forge->getConnection()->getCharset();
                $options['collation'] ??= $this->forge->getConnection()->getCollation();
                break;
            default:
                $options['charset'] = null;
                $options['collation'] = null;
                break;
        }

        switch ($options['type']) {
            case 'bit':
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
                $options['length'] ??= $options['unsigned'] ? 10 : 11;
                break;
            case 'bigint':
                $options['length'] ??= $options['unsigned'] ? 19 : 20;
                break;
            case 'varchar':
                $options['length'] ??= 80;
                break;
            case 'tinyblob':
            case 'tinytext':
                $options['length'] = 255;
                break;
            case 'blob':
            case 'text':
                $options['length'] = 65535;
                break;
            case 'mediumblob':
            case 'mediumtext':
                $options['length'] = 16777215;
                break;
            case 'longblob':
            case 'longtext':
                $options['length'] = 4294967295;
                break;
            case 'binary':
            case 'varbinary':
                break;
            default:
                $options['length'] = null;
                break;
        }

        switch ($options['type']) {
            case 'decimal':
                $options['precision'] ??= 0;
                break;
            case 'bit':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                $options['precision'] = 0;
                break;
            default:
                $options['precision'] = null;
                break;
        }

        switch ($options['type']) {
            case 'enum':
            case 'set':
                $options['values'] ??= [];
                $options['values'] = (array) $options['values'];
                break;
            default:
                $options['values'] = null;
                break;
        }

        switch ($options['type']) {
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
                $options['unsigned'] = false;
                break;
        }

        $options['unsigned'] = (bool) $options['unsigned'];
        $options['comment'] = (string) $options['comment'];

        return $options;
    }

    /**
     * Parse index options.
     *
     * @param array $options The index options.
     * @param string|null $index The index name.
     * @return array The parsed options.
     */
    public function parseIndexOptions(array $options = [], string|null $index = null): array
    {
        if ($index === 'PRIMARY') {
            $options['primary'] = true;
        }

        $options = parent::parseIndexOptions($options, $index);

        $options['type'] ??= 'btree';
        $options['type'] = strtolower($options['type']);

        return $options;
    }

    /**
     * Parse table options.
     *
     * @param array $options The table options.
     * @return array The parsed options.
     */
    public function parseTableOptions(array $options = []): array
    {
        $options['engine'] ??= 'InnoDB';
        $options['charset'] ??= $this->forge->getConnection()->getCharset();
        $options['collation'] ??= $this->forge->getConnection()->getCollation();
        $options['comment'] ??= '';

        $options['comment'] = (string) $options['comment'];

        return $options;
    }
}
