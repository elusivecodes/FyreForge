<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Postgres;

use Fyre\Forge\Forge;

use function is_numeric;

/**
 * PostgresForge
 */
class PostgresForge extends Forge
{
    /**
     * Add a column to a table.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function addColumn(string $table, string $column, array $options = []): bool
    {
        $generator = $this->generator();
        $options = $generator->parseColumnOptions($options);

        if (!$options['comment']) {
            return parent::addColumn($table, $column, $options);
        }

        $this->connection->transactional(function() use ($table, $column, $options) {
            parent::addColumn($table, $column, $options);
            $this->commentOnColumn($table, $column, $options['comment']);
        });

        return true;
    }

    /**
     * Add a foreign key to a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return bool TRUE if the query was successful.
     */
    public function addForeignKey(string $table, string $foreignKey, array $options = []): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildAddForeignKey($foreignKey, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Add an index to a table.
     *
     * @param string $table The table name.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return bool TRUE if the query was successful.
     */
    public function addIndex(string $table, string $index, array $options = []): bool
    {
        $sql = $this->generator()->buildCreateIndex($table, $index, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Alter a column's auto increment.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param bool $autoIncrement Whether to auto increment the column.
     * @return bool TRUE if the query was successful.
     */
    public function alterColumnAutoIncrement(string $table, string $column, bool $autoIncrement): bool
    {
        $generator = $this->generator();

        $alterSql = $generator->buildAlterColumnAutoIncrement($column, $autoIncrement);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Alter a column's default value.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param string|null $default The default value.
     * @return bool TRUE if the query was successful.
     */
    public function alterColumnDefault(string $table, string $column, string|null $default): bool
    {
        $generator = $this->generator();

        $alterSql = $generator->buildAlterColumnDefault($column, $default);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Alter whether a column is nullable.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param bool $nullable Whether the column is nullable.
     * @return bool TRUE if the query was successful.
     */
    public function alterColumnNullable(string $table, string $column, bool $nullable): bool
    {
        $generator = $this->generator();

        $alterSql = $generator->buildAlterColumnNullable($column, $nullable);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Alter a column's type.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return bool TRUE if the query was successful.
     */
    public function alterColumnType(string $table, string $column, array $options): bool
    {
        $generator = $this->generator();

        $alterSql = $generator->buildAlterColumnType($column, $options);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return PostgresTableForge The PostgresTableForge.
     */
    public function build(string $tableName, array $options = []): PostgresTableForge
    {
        return new PostgresTableForge($this, $tableName, $options);
    }

    /**
     * Set the comment for a column.
     *
     * @param string $table The table name.
     * @param string $column The column name.
     * @param string|null $comment The column comment.
     * @return bool TRUE if the query was successful.
     */
    public function commentOnColumn(string $table, string $column, string|null $comment)
    {
        $sql = $this->generator()->buildCommentOnColumn($table, $column, $comment);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Set the comment for a table.
     *
     * @param string $table The table name.
     * @param string|null $comment The table comment.
     * @return bool TRUE if the query was successful.
     */
    public function commentOnTable(string $table, string|null $comment)
    {
        $sql = $this->generator()->buildCommentOnTable($table, $comment);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Create a new schema.
     *
     * @param string $schema The schema name.
     * @param array $options The schema options.
     * @return bool TRUE if the query was successful.
     */
    public function createSchema(string $schema, array $options = []): bool
    {
        $sql = $this->generator()->buildCreateSchema($schema, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Create a new table.
     *
     * @param string $table The table name.
     * @param array $columns The table columns.
     * @param array $options The table options.
     * @return bool TRUE if the query was successful.
     */
    public function createTable(string $table, array $columns, array $options = []): bool
    {
        $generator = $this->generator();

        $options = $generator->parseTableOptions($options);

        $options['indexes'] ??= [];

        $constraints = [];
        $indexes = [];

        foreach ($options['indexes'] as $index => $indexOptions) {
            if (is_numeric($index)) {
                $index = $indexOptions;
                $indexOptions = [];
            }

            $indexOptions = $generator->parseIndexOptions($indexOptions);

            if ($indexOptions['primary'] || $indexOptions['unique']) {
                $constraints[$index] = $indexOptions;
            } else {
                $indexes[$index] = $indexOptions;
            }
        }

        if ($indexes === [] && !$options['comment']) {
            return parent::createTable($table, $columns, $options);
        }

        $options['indexes'] = $constraints;

        $this->connection->transactional(function() use ($table, $columns, $options, $indexes) {
            parent::createTable($table, $columns, $options);

            foreach ($indexes as $index => $indexOptions) {
                $this->addIndex($table, $index, $indexOptions);
            }

            if ($options['comment']) {
                $this->commentOnTable($table, $options['comment']);
            }
        });

        return true;
    }

    /**
     * Drop a constraint from a table.
     *
     * @param string $table The table name.
     * @param string $constraint The constraint name.
     * @return bool TRUE if the query was successful.
     */
    public function dropConstraint(string $table, string $constraint): bool
    {
        $generator = $this->generator();
        $alterSql = $generator->buildDropConstraint($constraint);
        $sql = $generator->buildAlterTable($table, [$alterSql]);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Drop a foreign key from a table.
     *
     * @param string $table The table name.
     * @param string $foreignKey The foreign key name.
     * @return bool TRUE if the query was successful.
     */
    public function dropForeignKey(string $table, string $foreignKey): bool
    {
        return $this->dropConstraint($table, $foreignKey);
    }

    /**
     * Drop a primary key from a table.
     *
     * @param string $table The table name.
     * @return bool TRUE if the query was successful.
     */
    public function dropPrimaryKey(string $table): bool
    {
        return $this->dropConstraint($table, $table.'_pkey');
    }

    /**
     * Drop a schema.
     *
     * @param string $schema The schema name.
     * @param array $options The options for dropping the schema.
     * @return bool TRUE if the query was successful.
     */
    public function dropSchema(string $schema, array $options = []): bool
    {
        $sql = $this->generator()->buildDropSchema($schema, $options);

        return (bool) $this->connection->query($sql);
    }

    /**
     * Get the forge query generator.
     *
     * @return PostgresForgeQueryGenerator The query generator.
     */
    public function generator(): PostgresForgeQueryGenerator
    {
        return $this->generator ??= new PostgresForgeQueryGenerator($this);
    }
}
