<?php
declare(strict_types=1);

namespace Fyre\Forge;

/**
 * TableForgeInterface
 */
interface TableForgeInterface
{

    /**
     * New TableForge constructor.
     * @param ForgeInterface $forge The Forge.
     * @param string $tableName The table name.
     * @param array $options The table options.
     */
    public function __construct(ForgeInterface $forge, string $tableName, array $options = []);

    /**
     * Add a column to the table.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the column already exists.
     */
    public function addColumn(string $column, array $options = []): static;

    /**
     * Add a foreign key to the table.
     * @param string $foreignKey The foreign key name.
     * @param array $options The foreign key options.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the foreign key already exists.
     */
    public function addForeignKey(string $foreignKey, array $options = []): static;

    /**
     * Add an index to the table.
     * @param string $index The index name.
     * @param array $options The index options.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the index already exists.
     */
    public function addIndex(string $index, array $options = []): static;

    /**
     * Change a table column.
     * @param string $column The column name.
     * @param array $options The column options.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the column does not exist.
     */
    public function changeColumn(string $column, array $options): static;

    /**
     * Clear data from the cache.
     * @return TableForgeInterface The TableForge.
     */
    public function clear(): static;

    /**
     * Drop the table.
     * @return TableForgeInterface The TableForge.
     */
    public function drop(): static;

    /**
     * Drop a column from the table.
     * @param string $column The column name.
     * @param array $options The options for dropping the table.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the column does not exist.
     */
    public function dropColumn(string $column, array $options = []): static;

    /**
     * Drop a foreign key from the table.
     * @param string $foreignKey The foreign key name.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the foreign key does not exist.
     */
    public function dropForeignKey(string $foreignKey): static;

    /**
     * Drop an index from the table.
     * @param string $index The index name.
     * @return TableForgeInterface The TableForge.
     * @throws ForgeException if the index does not exist.
     */
    public function dropIndex(string $index): static;

    /**
     * Generate and execute the SQL queries.
     * @return TableForgeInterface The TableForge.
     */
    public function execute(): static;

    /**
     * Rename the table.
     * @param string $tableName The new table name.
     * @return TableForgeInterface The TableForge.
     */
    public function rename(string $tableName): static;

    /**
     * Set the primary key.
     * @param string|array $columns The columns.
     * @return TableForgeInterface The TableForge.
     */
    public function setPrimaryKey(string|array $columns): static;

    /**
     * Generate the SQL queries.
     * @return array The SQL queries.
     */
    public function sql(): array;

}
