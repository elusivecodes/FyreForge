<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\DB\Types\StringType;

use function implode;
use function strtoupper;

/**
 * ForgeQueryGenerator
 */
abstract class ForgeQueryGenerator
{
    protected Forge $forge;

    /**
     * New ForgeQueryGenerator constructor.
     *
     * @param Forge $forge The forge.
     */
    public function __construct(Forge $forge)
    {
        $this->forge = $forge;
    }

    /**
     * Generate SQL for adding a column to a table.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildAddColumn(string $column, array $options = []): string
    {
        $sql = 'ADD COLUMN ';
        $sql .= $this->buildColumn($column, $options);

        return $sql;
    }

    /**
     * Generate SQL for altering a table.
     *
     * @param string $table The table name.
     * @param array $statements The statements.
     * @return string The SQL query.
     */
    public function buildAlterTable(string $table, array $statements): string
    {
        $sql = 'ALTER TABLE ';
        $sql .= $table;
        $sql .= ' ';
        $sql .= implode(', ', $statements);

        return $sql;
    }

    /**
     * Generate SQL for a column.
     *
     * @param string $column The column name.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    abstract public function buildColumn(string $column, array $options): string;

    /**
     * Generate SQL for creating a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return string The SQL query.
     */
    abstract public function buildCreateTable(string $table, array $columns, array $options = []): string;

    /**
     * Generate SQL for dropping a column from a table.
     *
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function buildDropColumn(string $column, array $options = []): string
    {
        $options['ifExists'] ??= false;

        $sql = 'DROP COLUMN ';

        if ($options['ifExists']) {
            $sql .= 'IF EXISTS ';
        }

        $sql .= $column;

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
     * Generate SQL for dropping a table.
     *
     * @param string $table The table name.
     * @param array $options The options for dropping the table.
     * @return string The SQL query.
     */
    public function buildDropTable(string $table, array $options = []): string
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
     * Generate SQL for a foreign key.
     *
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    public function buildForeignKey(string $foreignKey, array $options): string
    {
        $options = $this->parseForeignKeyOptions($options, $foreignKey);

        $sql = 'CONSTRAINT ';
        $sql .= $foreignKey;
        $sql .= ' FOREIGN KEY ';
        $sql .= '(';
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
     * Generate SQL for renaming a column.
     *
     * @param string $column The column name.
     * @param string $newColumn The new column name.
     * @return string The SQL query.
     */
    public function buildRenameColumn(string $column, string $newColumn): string
    {
        $sql = 'RENAME COLUMN ';
        $sql .= $column;
        $sql .= ' TO ';
        $sql .= $newColumn;

        return $sql;
    }

    /**
     * Generate SQL for renaming a table.
     *
     * @param string $table The new table name.
     * @return string The SQL query.
     */
    public function buildRenameTable(string $table): string
    {
        $sql = 'RENAME TO ';
        $sql .= $table;

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
        $options['type'] ??= StringType::class;
        $options['length'] ??= null;
        $options['precision'] ??= null;
        $options['nullable'] ??= false;
        $options['default'] ??= null;
        $options['autoIncrement'] ??= false;

        if ($options['length'] !== null) {
            $options['length'] = (int) $options['length'];
        }

        if ($options['precision'] !== null) {
            $options['precision'] = (int) $options['precision'];
        }

        if ($options['default'] !== null) {
            $options['default'] = (string) $options['default'];
        } else if ($options['nullable']) {
            $options['default'] = 'NULL';
        }

        $options['nullable'] = (bool) $options['nullable'];
        $options['autoIncrement'] = (bool) $options['autoIncrement'];

        return $options;
    }

    /**
     * Parse foreign key options.
     *
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
     *
     * @param array $options The index options.
     * @param string|null $index The index name.
     * @return array The parsed options.
     */
    public function parseIndexOptions(array $options = [], string|null $index = null): array
    {
        $options['columns'] ??= [];
        $options['unique'] ??= false;
        $options['primary'] ??= false;

        $options['unique'] = (bool) $options['unique'];
        $options['primary'] = (bool) $options['primary'];

        if ($options['primary']) {
            $options['unique'] = true;
        }

        $options['columns'] = (array) $options['columns'];

        if ($index && $options['columns'] === []) {
            $options['columns'] = [$index];
        }

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
        return $options;
    }
}
