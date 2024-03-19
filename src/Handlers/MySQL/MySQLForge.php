<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\MySQL;

use Fyre\Forge\Forge;

use function array_keys;
use function array_map;
use function implode;
use function is_numeric;
use function preg_replace_callback;
use function strtolower;
use function strtoupper;

/**
 * MySQLForge
 */
class MySQLForge extends Forge
{

    /**
     * Generate SQL for adding a column to a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function addColumnSql(string $table, string $column, array $options = []): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' ADD COLUMN ';
        $sql .= $this->prepareColumnSql($column, $options);

        return $sql;
    }

    /**
     * Generate SQL for adding a foreign key to a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    public function addForeignKeySql(string $table, string $foreignKey, array $options = []): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' ADD FOREIGN KEY ';
        $sql .= $foreignKey;
        $sql .= $this->prepareForeignKeySql($foreignKey, $options);

        return $sql;
    }

    /**
     * Generate SQL for adding an index to a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The SQL query.
     */
    public function addIndexSql(string $table, string $index, array $options = []): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' ADD ';
        $sql .= $this->prepareIndexSql($index, $options);

        return $sql;
    }

    /**
     * Generate SQL for altering a table.
     * @param string $table The table name.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    public function alterTableSql(string $table, array $options = []): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= $this->prepareTableSql($options);

        return $sql;
    }

    /**
     * Build a table schema.
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return MySQLTableForge The MySQLTableForge.
     */
    public function build(string $tableName, array $options = []): MySQLTableForge
    {
        return new MySQLTableForge($this, $tableName, $options);
    }

    /**
     * Generate SQL for changing a table column.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function changeColumnSql(string $table, string $column, array $options): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' CHANGE COLUMN ';
        $sql .= $column;
        $sql .= ' ';
        $sql .= $this->prepareColumnSql($options['name'] ?? $column, $options);

        return $sql;
    }

    /**
     * Generate SQL for creating a new schema.
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return string The SQL query.
     */
    public function createSchemaSql(string $schema, array $options = []): string
    {
        $options['ifNotExists'] ??= false;
        $options['charset'] ??= $this->connection->getCharset();
        $options['collation'] ??= $this->connection->getCollation();

        $sql = 'CREATE SCHEMA ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $schema;

        if ($options['charset']) {
            $sql .= ' CHARACTER SET = '.$this->connection->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE = '.$this->connection->quote($options['collation']);
        }

        return $sql;
    }

    /**
     * Generate SQL for creating a new table.
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    public function createTableSql(string $table, array $columns, array $options = []): string
    {
        $options['indexes'] ??= [];
        $options['foreignKeys'] ??= [];
        $options['ifNotExists'] ??= false;

        $definitions = array_map(
            fn(string $column, array $options) => $this->prepareColumnSql($column, $options),
            array_keys($columns),
            $columns
        );

        foreach ($options['indexes'] AS $index => $indexOptions) {
            if (is_numeric($index)) {
                $index = $indexOptions;
                $indexOptions = [];
            }

            $definitions[] = $this->prepareIndexSql($index, $indexOptions);
        }

        foreach ($options['foreignKeys'] AS $foreignKey => $foreignKeyOptions) {
            $definitions[] = 'CONSTRAINT '.$foreignKey.' FOREIGN KEY'.$this->prepareForeignKeySql($foreignKey, $foreignKeyOptions);
        }

        $options = $this->parseTableOptions($options);

        $sql = 'CREATE TABLE ';

        if ($options['ifNotExists']) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $table;

        $sql .= ' (';
        $sql .= implode(', ', $definitions);
        $sql .= ')';

        $sql .= $this->prepareTableSql($options);

        return $sql;
    }

    /**
     * Generate SQL for dropping a column from a table.
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function dropColumnSql(string $table, string $column, array $options = []): string
    {
        $options['ifExists'] ??= false;

        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' DROP COLUMN ';

        if ($options['ifExists']) {
            $sql .= 'IF EXISTS ';
        }

        $sql .= $column;

        return $sql;
    }

    /**
     * Generate SQL for dropping a foreign key from a table.
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return string The SQL query.
     */
    public function dropForeignKeySql(string $table, string $foreignKey): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' DROP FOREIGN KEY ';
        $sql .= $foreignKey;

        return $sql;
    }

    /**
     * Generate SQL for dropping an index from a table.
     * @param string $table The table name.
     * @param string $index The index name.
     * @return string The SQL query.
     */
    public function dropIndexSql(string $table, string $index): string
    {
        $sql = 'DROP INDEX ';
        $sql .= $index;
        $sql .= ' ON ';
        $sql .= $table;

        return $sql;
    }

    /**
     * Generate SQL for dropping a schema.
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return string The SQL query.
     */
    public function dropSchemaSql(string $schema, array $options = []): string
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
     * Generate SQL for dropping a table.
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function dropTableSql(string $table, array $options = []): string
    {
        $options['ifExists'] ??= false;

        $sql = 'DROP TABLE ';

        if ($options['ifExists']) {
            $sql .= 'IF EXISTS ';
        }

        $sql .= $table;

        return $sql;
    }

    /**
     * Parse column options.
     * @param array $options The column options.
     * @return array The parsed options.
     */
    public function parseColumnOptions(array $options = []): array
    {
        $options['type'] = strtolower($options['type'] ?? 'varchar');

        $options['length'] ??= null;
        $options['precision'] ??= null;
        $options['nullable'] ??= false;
        $options['unsigned'] ??= false;
        $options['default'] ??= null;
        $options['charset'] ??= null;
        $options['collation'] ??= null;
        $options['extra'] = strtolower($options['extra'] ?? '');
        $options['comment'] ??= '';

        if ($options['default'] !== null) {
            $options['default'] = preg_replace_callback(
                '/(["\']).*?[^\\\\]\1|([A-Z]+)/',
                fn(array $match): string => $match[1] || $match[2] === 'NULL' ?
                    $match[0] :
                    strtolower($match[2]),
                $options['default']
            );
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
                $options['charset'] ??= $this->connection->getCharset();
                $options['collation'] ??= $this->connection->getCollation();
                break;
            default:
                $options['charset'] = null;
                $options['collation'] = null;
                break;
        }

        $defaultLength = $options['length'] === null;
        switch ($options['type']) {
            case 'bit':
                $options['length'] = 1;
                break;
            case 'tinyint':
                $options['length'] ??= 3;
                break;
            case 'smallint':
                $options['length'] ??= 5;
                break;
            case 'mediumint':
                $options['length'] ??= 7;
                break;
            case 'int':
                $options['length'] ??= 10;
                break;
            case 'bigint':
                $options['length'] ??= 19;
                break;
            case 'decimal':
                $options['length'] ??= 10;
                break;
            case 'double':
            case 'float':
            case 'binary':
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
                $options['precision'] ??= 0;
                break;
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'float':
            case 'double':
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
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'decimal':
            case 'float':
            case 'double':
                if (!$options['unsigned'] && $options['length'] !== null && $defaultLength) {
                    $options['length']++;
                }

                break;
            default:
                $options['unsigned'] = false;
                break;
        }

        return $options;
    }

    /**
     * Parse foreign key options.
     * @param array $options The foreign key options.
     * @param string|null $foreignKey The foreign key name.
     * @return array The parsed options.
     */
    public function parseForeignKeyOptions(array $options = [], string|null $foreignKey = null): array
    {
        $options['columns'] ??= [];
        $options['referencedTable'] ??= '';
        $options['referencedColumns'] ??= [];
        $options['update'] ??= '';
        $options['delete'] ??= '';

        $options['columns'] = (array) $options['columns'];
        $options['referencedColumns'] = (array) $options['referencedColumns'];

        if ($foreignKey && $options['columns'] === []) {
            $options['columns'] = [$foreignKey];
        }

        return $options;
    }

    /**
     * Parse index options.
     * @param array $options The index options.
     * @param string|null $index The index name.
     * @return array The parsed options.
     */
    public function parseIndexOptions(array $options = [], string|null $index = null): array
    {
        $options['type'] ??= 'BTREE';
        $options['columns'] ??= [];
        $options['unique'] ??= false;

        $options['type'] = strtoupper($options['type']);
        $options['columns'] = (array) $options['columns'];

        if ($index && $options['columns'] === []) {
            $options['columns'] = [$index];
        }

        return $options;
    }

    /**
     * Parse table options.
     * @param array $options The table options.
     * @return array The parsed options.
     */
    public function parseTableOptions(array $options = []): array
    {
        $options['engine'] ??= 'InnoDB';
        $options['charset'] ??= $this->connection->getCharset();
        $options['collation'] ??= $this->connection->getCollation();
        $options['comment'] ??= '';

        return $options;
    }

    /**
     * Generate SQL for renaming a table.
     * @param string $table The old table name.
     * @param string $newTable The new table name.
     * @return string The SQL query.
     */
    public function renameTableSql(string $table, string $newTable): string
    {
        $sql = 'RENAME TABLE ';
        $sql .= $table;
        $sql .= ' TO ';
        $sql .= $newTable;

        return $sql;
    }

    /**
     * Prepare a column SQL.
     * @param string $column The column name.
     * @param array $options The Cclumn options.
     * @return string The column SQL.
     */
    protected function prepareColumnSql(string $column, array $options): string
    {
        $options = $this->parseColumnOptions($options);

        $options['after'] ??= null;
        $options['first'] ??= false;

        $sql = $column;
        $sql .= ' ';
        $sql .= strtoupper($options['type']);

        if ($options['length'] !== null) {
            $sql .= '(';
            $sql .= (int) $options['length'];

            if ($options['precision']) {
                $sql .= ',';
                $sql .= (int) $options['precision'];
            }

            $sql .= ')';
        }

        if ($options['values'] !== null) {
            $options['values'] = array_map(
                fn($value): string => $this->connection->quote((string) $value),
                $options['values']
            );

            $sql .= '(';
            $sql .= implode(',', $options['values']);
            $sql .= ')';
        }

        if ($options['unsigned']) {
            $sql .= ' UNSIGNED';
        }

        if ($options['charset']) {
            $sql .= ' CHARACTER SET '.$this->connection->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE '.$this->connection->quote($options['collation']);
        }

        if ($options['nullable']) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($options['default'] !== null) {
            $sql .= ' DEFAULT ';
            $sql .= preg_replace_callback(
                '/(["\']).*?[^\\\\]\1|([a-z]+)/',
                fn(array $match): string => $match[1] ?
                    $match[0] :
                    strtoupper($match[2]),
                $options['default']
            );
        }

        if ($options['extra']) {
            $sql .= ' '.strtoupper($options['extra']);
        }

        if ($options['comment'] !== '') {
            $sql .= ' COMMENT '.$this->connection->quote($options['comment']);
        }

        if ($options['after']) {
            $sql .= ' AFTER '.$options['after'];
        } else if ($options['first']) {
            $sql .= ' FIRST';
        }

        return $sql;
    }

    /**
     * Prepare a foreign key SQL.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The foreign key SQL.
     */
    protected function prepareForeignKeySql(string $foreignKey, array $options): string
    {
        $options = $this->parseForeignKeyOptions($options, $foreignKey);

        $sql = '';
        $sql .= ' (';
        $sql .= implode(', ', (array) $options['columns']);
        $sql .= ')';
        $sql .= ' REFERENCES ';
        $sql .= $options['referencedTable'];
        $sql .= ' (';
        $sql .= implode(', ', (array) $options['referencedColumns']);
        $sql .= ')';

        if ($options['update']) {
            $sql .= ' ON UPDATE ';
            $sql .= strtoupper($options['update']);
        }

        if ($options['delete']) {
            $sql .= ' ON DELETE ';
            $sql .= strtoupper($options['delete']);
        }

        return $sql;
    }

    /**
     * Prepare an index SQL.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return string The index SQL.
     */
    protected function prepareIndexSql(string $index, array $options): string
    {
        $options = $this->parseIndexOptions($options, $index);

        $sql = '';
        if ($index === 'PRIMARY') {
            $sql .= 'PRIMARY KEY';
        } else if ($options['type'] === 'FULLTEXT') {
            $sql .= 'FULLTEXT INDEX';
        } else if ($options['unique']) {
            $sql .= 'UNIQUE KEY';
        } else {
            $sql .= 'INDEX';
        }

        if ($index !== 'PRIMARY') {
            $sql .= ' ';
            $sql .= $index;
        }

        $sql .= ' (';
        $sql .= implode(', ', $options['columns']);
        $sql .= ')';

        if ($index !== 'PRIMARY' && $options['type'] !== 'FULLTEXT') {
            $sql .= ' USING ';
            $sql .= $options['type'];
        }

        return $sql;
    }

    /**
     * Prepate table SQL.
     * @param array $options The table options.
     * @return string The table SQL.
     */
    protected function prepareTableSql(array $options): string
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
            $sql .= ' DEFAULT CHARSET = '.$this->connection->quote($options['charset']);
        }

        if ($options['collation']) {
            $sql .= ' COLLATE = '.$this->connection->quote($options['collation']);
        }

        if ($options['comment'] !== '') {
            $sql .= ' COMMENT '.$this->connection->quote($options['comment']);
        }

        return $sql;
    }

}
