<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Sqlite;

use Fyre\Forge\Forge;

/**
 * SqliteForge
 */
class SqliteForge extends Forge
{
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
     * Build a table schema.
     *
     * @param string $tableName The table name.
     * @param array $options The table options.
     * @return SqliteTableForge The SqliteTableForge.
     */
    public function build(string $tableName, array $options = []): SqliteTableForge
    {
        return $this->container->build(SqliteTableForge::class, [
            'forge' => $this,
            'tableName' => $tableName,
            'options' => $options,
        ]);
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

        $options['indexes'] ??= [];

        $constraints = [];
        $indexes = [];

        foreach ($options['indexes'] as $index => $indexOptions) {
            if (is_numeric($index)) {
                $index = $indexOptions;
                $indexOptions = [];
            }

            $indexOptions = $generator->parseIndexOptions($indexOptions);

            if ($indexOptions['primary']) {
                $constraints[$index] = $indexOptions;
            } else {
                $indexes[$index] = $indexOptions;
            }
        }

        if ($indexes === []) {
            return parent::createTable($table, $columns, $options);
        }

        $options['indexes'] = $constraints;

        $this->connection->transactional(function() use ($table, $columns, $options, $indexes) {
            parent::createTable($table, $columns, $options);

            foreach ($indexes as $index => $indexOptions) {
                $this->addIndex($table, $index, $indexOptions);
            }
        });

        return true;
    }

    /**
     * Get the forge query generator.
     *
     * @return SqliteForgeQueryGenerator The query generator.
     */
    public function generator(): SqliteForgeQueryGenerator
    {
        return $this->generator ??= $this->container->build(SqliteForgeQueryGenerator::class, ['forge' => $this]);
    }
}
