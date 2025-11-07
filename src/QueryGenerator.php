<?php
declare(strict_types=1);

namespace Fyre\Forge;

use Fyre\Utility\Traits\MacroTrait;

use function implode;
use function strtoupper;

/**
 * QueryGenerator
 */
abstract class QueryGenerator
{
    use MacroTrait;

    /**
     * New QueryGenerator constructor.
     *
     * @param Forge $forge The forge.
     */
    public function __construct(
        protected Forge $forge
    ) {}

    /**
     * Generate SQL for adding a column to a table.
     *
     * @param Column $column The Column.
     * @param array $options The column options.
     * @return string The SQL query.
     */
    public function buildAddColumn(Column $column, array $options = []): string
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
     * @param Column $column The Column.
     * @return string The SQL query.
     */
    abstract public function buildColumn(Column $column): string;

    /**
     * Generate SQL for creating a new table.
     *
     * @param array $options The table options.
     * @param string $Table The Table.
     * @return string The SQL query.
     */
    abstract public function buildCreateTable(Table $table, array $options = []): string;

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
     * @param ForeignKey $foreignKey The ForeignKey.
     * @param array $options The foreign key options.
     * @return string The SQL query.
     */
    public function buildForeignKey(ForeignKey $foreignKey): string
    {
        $onUpdate = $foreignKey->getOnUpdate();
        $onDelete = $foreignKey->getOnDelete();

        $sql = 'CONSTRAINT ';
        $sql .= $foreignKey->getName();
        $sql .= ' FOREIGN KEY ';
        $sql .= '(';
        $sql .= implode(', ', $foreignKey->getColumns());
        $sql .= ')';
        $sql .= ' REFERENCES ';
        $sql .= $foreignKey->getReferencedTable();
        $sql .= ' (';
        $sql .= implode(', ', $foreignKey->getReferencedColumns());
        $sql .= ')';

        if ($onUpdate) {
            $sql .= ' ON UPDATE ';
            $sql .= strtoupper($onUpdate);
        }

        if ($onDelete) {
            $sql .= ' ON DELETE ';
            $sql .= strtoupper($onDelete);
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
}
