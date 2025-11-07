<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Forge\Column;
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\ForeignKey;
use Fyre\Forge\Index;
use Fyre\Forge\QueryGenerator;
use Fyre\Forge\Table;
use Override;

use function array_key_exists;
use function array_map;
use function implode;
use function str_starts_with;
use function strtoupper;

/**
 * MysqlQueryGenerator
 */
class MysqlQueryGenerator extends QueryGenerator
{
    /**
     * Generate SQL for adding a foreign key to a table.
     *
     * @param ForeignKey $foreignKey The ForeignKey.
     * @return string The SQL query.
     */
    public function buildAddForeignKey(ForeignKey $foreignKey): string
    {
        $sql = 'ADD ';
        $sql .= $this->buildForeignKey($foreignKey);

        return $sql;
    }

    /**
     * Generate SQL for adding an index to a table.
     *
     * @param Index $index The Index.
     * @return string The SQL query.
     */
    public function buildAddIndex(Index $index): string
    {
        $sql = 'ADD ';
        $sql .= $this->buildIndex($index);

        return $sql;
    }

    /**
     * Generate SQL for changing a table column.
     *
     * @param Column $column The Column.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildChangeColumn(Column $column, array $options = []): string
    {
        $sql = 'CHANGE COLUMN ';
        $sql .= $options['name'] ?? $column->getName();
        $sql .= ' ';
        $sql .= $this->buildColumn($column, $options);

        return $sql;
    }

    /**
     * Generate SQL for a column.
     *
     * @param Column $column The Column.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    #[Override]
    public function buildColumn(Column $column, array $options = []): string
    {
        $options['after'] ??= null;
        $options['first'] ??= false;
        $options['forceComment'] ??= false;

        $type = $column->getType();

        $sql = $column->getName();
        $sql .= ' ';
        $sql .= strtoupper($type);

        $length = $column->getLength();
        $values = $column->getValues();

        if ($length !== null) {
            switch ($type) {
                case 'bit':
                case 'char':
                case 'varchar':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                    $sql .= '(';
                    $sql .= $length;
                    $sql .= ')';
                    break;
                case 'decimal':
                    $sql .= '(';
                    $sql .= $length;
                    $sql .= ',';
                    $sql .= $column->getPrecision();
                    $sql .= ')';
                    break;
            }
        } else if ($values !== null) {
            switch ($type) {
                case 'enum':
                case 'set':
                    $values = array_map(
                        fn($value): string => $this->forge->getConnection()->quote((string) $value),
                        $values
                    );

                    $sql .= '(';
                    $sql .= implode(',', $values);
                    $sql .= ')';
                    break;
            }
        }

        if ($column->isUnsigned()) {
            $sql .= ' UNSIGNED';
        }

        $charset = $column->getCharset();

        if ($charset) {
            $sql .= ' CHARACTER SET '.$this->forge->getConnection()->quote($charset);
        }

        $collation = $column->getCollation();

        if ($collation) {
            $sql .= ' COLLATE '.$this->forge->getConnection()->quote($collation);
        }

        if ($column->isNullable()) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        $default = $column->getDefault();

        if ($default !== null) {
            $sql .= ' DEFAULT ';
            switch ($type) {
                case 'binary':
                case 'blob':
                case 'geometry':
                case 'json':
                case 'linestring':
                case 'longblob':
                case 'longtext':
                case 'mediumblob':
                case 'mediumtext':
                case 'point':
                case 'polygon':
                case 'text':
                case 'tinyblob':
                case 'tinytext':
                case 'varbinary':
                    $sql .= '('.$default.')';
                    break;
                default:
                    if (str_starts_with($default, 'current_timestamp')) {
                        $sql .= strtoupper($default);
                    } else {
                        $sql .= $default;
                    }
                    break;
            }
        }

        if ($column->isAutoIncrement()) {
            $sql .= ' AUTO_INCREMENT';
        }

        $comment = $column->getComment();

        if ($comment || $options['forceComment']) {
            $sql .= ' COMMENT '.$this->forge->getConnection()->quote($comment);
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
     * @param Table $table The Table.
     * @param array $options The table options.
     * @param array $columns The table columns.
     * @return string The SQL query.
     */
    #[Override]
    public function buildCreateTable(Table $table, array $options = []): string
    {
        $options['ifNotExists'] ??= false;

        $columns = $table->columns();
        $indexes = $table->indexes();
        $foreignKeys = $table->foreignKeys();

        $definitions = array_map(
            fn(Column $column) => $this->buildColumn($column),
            $columns
        );

        foreach ($indexes as $name => $index) {
            if (array_key_exists($name, $foreignKeys)) {
                continue;
            }

            $definitions[] = $this->buildIndex($index);
        }

        foreach ($foreignKeys as $foreignKey) {
            $definitions[] = $this->buildForeignKey($foreignKey);
        }

        $sql = 'CREATE TABLE ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $table->getName();

        $sql .= ' (';
        $sql .= implode(', ', $definitions);
        $sql .= ')';

        $engine = $table->getEngine();

        if ($engine) {
            $sql .= ' '.$this->buildTableEngine($engine);
        }

        $charset = $table->getCharset();

        if ($charset) {
            $sql .= ' '.$this->buildTableCharset($charset);
        }

        $collation = $table->getCollation();

        if ($collation) {
            $sql .= ' '.$this->buildTableCollation($collation);
        }

        $comment = $table->getComment();

        if ($comment) {
            $sql .= ' '.$this->buildTableComment($comment);
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
     * @param Index $index The Index.
     * @return string The SQL query.
     *
     * @throws ForgeException if primary key index type is not valid.
     */
    public function buildIndex(Index $index): string
    {
        $columns = implode(', ', $index->getColumns());

        $type = $index->getType();

        if ($index->isPrimary()) {
            if ($type !== 'btree') {
                throw ForgeException::forInvalidIndexType($type);
            }

            return 'PRIMARY KEY ('.$columns.')';
        }

        $name = $index->getName();

        if ($index->isUnique()) {
            return 'CONSTRAINT '.$name.' UNIQUE KEY ('.$columns.') USING '.strtoupper($type);
        }

        switch ($type) {
            case 'fulltext':
                return 'FULLTEXT INDEX '.$name.' ('.$columns.')';
            case 'spatial':
                return 'SPATIAL INDEX '.$name.' ('.$columns.')';
            default:
                return 'INDEX '.$name.' ('.$columns.') USING '.strtoupper($type);
        }
    }

    /**
     * Generate SQL for the table character set option.
     *
     * @param string $charset The character set.
     * @return string The SQL query.
     */
    public function buildTableCharset(string $charset): string
    {
        return 'DEFAULT CHARSET = '.$this->forge->getConnection()->quote($charset);
    }

    /**
     * Generate SQL for the table collation option.
     *
     * @param string $collation The collation.
     * @return string The SQL query.
     */
    public function buildTableCollation(string $collation): string
    {
        return 'COLLATE = '.$this->forge->getConnection()->quote($collation);
    }

    /**
     * Generate SQL for the table comment option.
     *
     * @param string $comment The comment.
     * @return string The SQL query.
     */
    public function buildTableComment(string $comment): string
    {
        return 'COMMENT '.$this->forge->getConnection()->quote($comment);
    }

    /**
     * Generate SQL for the table engine option.
     *
     * @param string $engine The engine.
     * @return string The SQL query.
     */
    public function buildTableEngine(string $engine): string
    {
        return 'ENGINE = '.$engine;
    }
}
