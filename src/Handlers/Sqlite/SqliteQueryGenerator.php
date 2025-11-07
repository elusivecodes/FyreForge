<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

use Fyre\Forge\Column;
use Fyre\Forge\Exceptions\ForgeException;
use Fyre\Forge\Index;
use Fyre\Forge\QueryGenerator;
use Fyre\Forge\Table;
use Override;

use function array_key_exists;
use function implode;
use function str_starts_with;
use function strtoupper;

/**
 * SqliteQueryGenerator
 */
class SqliteQueryGenerator extends QueryGenerator
{
    /**
     * Generate SQL for adding a constraint.
     *
     * @param Index $index The Index.
     * @return string The SQL query.
     */
    public function buildAddConstraint(Index $index)
    {
        $sql = 'ADD CONSTRAINT ';
        $sql .= $this->buildConstraint($index);

        return $sql;
    }

    /**
     * Generate SQL for a column.
     *
     * @param Column $column The Column.
     * @return string The SQL query.
     */
    #[Override]
    public function buildColumn(Column $column): string
    {
        $type = $column->getType();

        $sql = $column->getName();

        if ($column->isUnsigned()) {
            $sql .= ' UNSIGNED';
        }

        $sql .= ' ';
        $sql .= strtoupper($type);

        $length = $column->getLength();

        if ($length !== null) {
            switch ($type) {
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
                    $sql .= $length;
                    $sql .= ')';
                    break;
                case 'decimal':
                case 'numeric':
                    $sql .= '(';
                    $sql .= $length;
                    $sql .= ',';
                    $sql .= $column->getPrecision();
                    $sql .= ')';
                    break;
            }
        }

        if ($column->isNullable()) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        $default = $column->getDefault();

        if ($default !== null) {
            $sql .= ' DEFAULT ';
            if (str_starts_with($default, 'current_timestamp')) {
                $sql .= strtoupper($default);
            } else {
                $sql .= $default;
            }
        }

        return $sql;
    }

    /**
     * Generate SQL for a constraint.
     *
     * @param Index $index The Index.
     * @return string The SQL query.
     *
     * @throws ForgeException if the constraint is not valid.
     */
    public function buildConstraint(Index $index): string
    {
        if ($index->isPrimary()) {
            $sql = 'PRIMARY';
        } else if ($index->isUnique()) {
            $sql = $index->getName();
            $sql .= ' UNIQUE';
        } else {
            throw ForgeException::forInvalidConstraint($index->getName());
        }

        $sql .= ' KEY (';
        $sql .= implode(', ', $index->getColumns());
        $sql .= ')';

        return $sql;
    }

    /**
     * Generate SQL for creating a table index.
     *
     * @param Index $index The Index.
     * @return string The SQL query.
     *
     * @throws ForgeException if the index is a primary key.
     */
    public function buildCreateIndex(Index $index): string
    {
        if ($index->isPrimary()) {
            throw new ForgeException('Primary keys cannot be added to SQLite tables: '.$index->getName());
        }

        $sql = 'CREATE ';

        if ($index->isUnique()) {
            $sql .= 'UNIQUE ';
        }

        $sql .= 'INDEX ';
        $sql .= $index->getName();
        $sql .= ' ON ';
        $sql .= $index->getTable()->getName();
        $sql .= ' (';
        $sql .= implode(', ', $index->getColumns());
        $sql .= ')';

        return $sql;
    }

    /**
     * Generate SQL for creating a new table.
     *
     * @param Table $table The Table.
     * @param array $options The table options.
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

            if ($index->isPrimary()) {
                $definitions[] = $this->buildConstraint($index);
            } else {
                continue;
            }
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

        return $sql;
    }
}
