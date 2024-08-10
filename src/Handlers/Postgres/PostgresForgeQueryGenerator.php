<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\ForgeQueryGenerator;

use function array_keys;
use function array_map;
use function implode;
use function is_numeric;
use function str_starts_with;
use function strtolower;
use function strtoupper;

/**
 * PostgresForgeQueryGenerator
 */
class PostgresForgeQueryGenerator extends ForgeQueryGenerator
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
     * Generate SQL for changing a column's auto increment.
     *
     * @param string $column The column name.
     * @param bool $autoIncrement Whether to auto increment the column.
     * @return string The SQL query.
     */
    public function buildAlterColumnAutoIncrement(string $column, bool $autoIncrement): string
    {
        $sql = 'ALTER COLUMN ';
        $sql .= $column;

        if ($autoIncrement) {
            $sql .= ' ADD GENERATED BY DEFAULT AS IDENTITY';
        } else {
            $sql .= ' DROP IDENTITY';
        }

        return $sql;
    }

    /**
     * Generate SQL for changing a column's default value.
     *
     * @param string $column The column name.
     * @param string|null $default The default value.
     * @return string The SQL query.
     */
    public function buildAlterColumnDefault(string $column, string|null $default): string
    {
        $sql = 'ALTER COLUMN ';
        $sql .= $column;

        if ($default === null) {
            $sql .= ' DROP DEFAULT';
        } else {
            $sql .= ' SET DEFAULT ';
            $sql .= $default;
        }

        return $sql;
    }

    /**
     * Generate SQL for changing whether a column is nullable.
     *
     * @param string $column The column name.
     * @param bool $nullable Whether the column is nullable.
     * @return string The SQL query.
     */
    public function buildAlterColumnNullable(string $column, bool $nullable): string
    {
        $sql = 'ALTER COLUMN ';
        $sql .= $column;

        if ($nullable) {
            $sql .= ' DROP ';
        } else {
            $sql .= ' SET ';
        }

        $sql .= ' NOT NULL';

        return $sql;
    }

    /**
     * Generate SQL for changeing a column's type.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildAlterColumnType(string $column, array $options): string
    {
        $options = $this->parseColumnOptions($options);

        $options['cast'] ??= false;

        $sql = 'ALTER COLUMN ';
        $sql .= $column;
        $sql .= ' TYPE ';
        $sql .= static::buildColumnType($options['type'], $options['length'], $options['precision']);

        if ($options['cast']) {
            $sql .= ' USING CAST('.$column.' AS '.strtoupper($options['type']).')';
        }

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
        $sql .= ' ';
        $sql .= static::buildColumnType($options['type'], $options['length'], $options['precision']);

        if ($options['nullable']) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($options['default'] !== null) {
            $sql .= ' DEFAULT ';
            $sql .= $options['default'];
        }

        if ($options['autoIncrement']) {
            $sql .= ' GENERATED BY DEFAULT AS IDENTITY';
        }

        return $sql;
    }

    /**
     * Generate SQL for a column comment.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param string|null $comment The column comment.
     * @return string The SQL query.
     */
    public function buildCommentOnColumn(string $table, string $column, string|null $comment): string
    {
        $sql = 'COMMENT ON COLUMN ';
        $sql .= $table;
        $sql .= '.';
        $sql .= $column;
        $sql .= ' IS ';

        if ($comment) {
            $sql .= $this->forge->getConnection()->quote($comment);
        } else {
            $sql .= 'NULL';
        }

        return $sql;
    }

    /**
     * Generate SQL for a table comment.
     *
     * @param string $table The table name.
     * @param string|null $comment The table comment.
     * @return string The SQL query.
     */
    public function buildCommentOnTable(string $table, string|null $comment): string
    {
        $sql = 'COMMENT ON TABLE ';
        $sql .= $table;
        $sql .= ' IS ';

        if ($comment) {
            $sql .= $this->forge->getConnection()->quote($comment);
        } else {
            $sql .= 'NULL';
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

        if (!$options['primary'] && !$options['unique']) {
            throw ForgeException::forInvalidConstraint($index);
        }

        if ($options['type'] !== 'btree') {
            throw ForgeException::forInvalidIndexType($options['type']);
        }

        $columns = implode(', ', $options['columns']);

        if ($options['primary']) {
            return 'PRIMARY KEY ('.$columns.')';
        }

        if ($options['unique']) {
            return $index.' UNIQUE ('.$columns.')';
        }
    }

    /**
     * Generate SQL for creating a table index.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildCreateIndex(string $table, string $index, array $options): string
    {
        $options = $this->parseIndexOptions($options, $index);

        if ($options['primary']) {
            $sql = 'ADD '.$this->buildConstraint($index, $options);

            return $this->buildAlterTable($table, [$sql]);
        }

        if ($options['unique']) {
            $sql = $this->buildAddConstraint($index, $options);

            return $this->buildAlterTable($table, [$sql]);
        }

        $sql = 'CREATE INDEX ';
        $sql .= $index;
        $sql .= ' ON ';
        $sql .= $table;
        $sql .= ' USING ';
        $sql .= strtoupper($options['type']);
        $sql .= ' (';
        $sql .= implode(', ', $options['columns']);
        $sql .= ')';

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

        $sql = 'CREATE SCHEMA ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $schema;

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
            } else if ($indexOptions['unique']) {
                $definitions[] = 'CONSTRAINT '.$this->buildConstraint($index, $indexOptions);
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
     * Generate SQL for dropping a constraint from a table.
     *
     * @param string $index The index name.
     * @return string The SQL query.
     */
    public function buildDropConstraint(string $index): string
    {
        $sql = 'DROP CONSTRAINT ';
        $sql .= $index;

        return $sql;
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
     * Parse column options.
     *
     * @param array $options The column options.
     * @return array The parsed options.
     */
    public function parseColumnOptions(array $options = []): array
    {
        $options = parent::parseColumnOptions($options);

        $options['comment'] ??= '';

        $type = $options['type'];

        $options['type'] = static::$typeAliases[$type] ?? $type;

        if ($options['default'] !== null) {
            $options['default'] = (string) $options['default'];
            $default = strtoupper($options['default']);
            if (str_starts_with($default, 'CURRENT_TIMESTAMP') || $default === 'NULL') {
                $options['default'] = $default;
            }
        }

        switch ($options['type']) {
            case 'bit':
            case 'character':
                $options['length'] ??= 1;
                break;
            case 'smallint':
            case 'smallserial':
                $options['length'] = 6;
                break;
            case 'integer':
            case 'serial':
                $options['length'] = 11;
                break;
            case 'bigint':
            case 'bigserial':
                $options['length'] = 20;
                break;
            case 'numeric':
                $options['length'] ??= 10;
                break;
            case 'character varying':
                $options['length'] ??= 80;
                break;
            case 'bit varying':
                break;
            default:
                $options['length'] = null;
                break;
        }

        switch ($options['type']) {
            case 'numeric':
                $options['precision'] ??= 0;
                break;
            case 'smallint':
            case 'smallserial':
            case 'integer':
            case 'serial':
            case 'bigint':
            case 'bigserial':
                $options['precision'] = 0;
                break;
            case 'time without time zone':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                $options['precision'] ??= 6;
                break;
            default:
                $options['precision'] = null;
                break;
        }

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
        $options['comment'] ??= '';

        $options['comment'] = (string) $options['comment'];

        return $options;
    }

    /**
     * Generate SQL for a column type.
     *
     * @param string $type The column type.
     * @param int|null $length The column length.
     * @param int|null $precision The column precision.
     * @return string The SQL query.
     */
    protected static function buildColumnType(string $type, int|null $length = null, int|null $precision = null): string
    {
        $sql = strtoupper($type);

        if ($length !== null) {
            switch ($type) {
                case 'bpchar':
                case 'character':
                case 'character varying':
                    $sql .= '(';
                    $sql .= $length;
                    $sql .= ')';
                    break;
                case 'numeric':
                    $sql .= '(';
                    $sql .= $length;
                    if ($precision) {
                        $sql .= ',';
                        $sql .= $precision;
                    }
                    $sql .= ')';
                    break;
            }
        } else if ($precision !== null) {
            switch ($type) {
                case 'time without time zone':
                    $sql = 'TIME';
                    $sql .= '(';
                    $sql .= $precision;
                    $sql .= ')';
                    $sql .= ' WITHOUT TIME ZONE';
                    break;
                case 'timestamp without time zone':
                    $sql = 'TIMESTAMP';
                    $sql .= '(';
                    $sql .= $precision;
                    $sql .= ')';
                    $sql .= ' WITHOUT TIME ZONE';
                    break;
                case 'timestamp with time zone':
                    $sql = 'TIMESTAMP';
                    $sql .= '(';
                    $sql .= $precision;
                    $sql .= ')';
                    $sql .= ' WITH TIME ZONE';
                    break;
            }
        }

        return $sql;
    }
}