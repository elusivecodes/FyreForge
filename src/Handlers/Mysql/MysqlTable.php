<?php
declare(strict_types=1);

namespace Fyre\Forge\Handlers\Mysql;

use Fyre\Container\Container;
use Fyre\Forge\Forge;
use Fyre\Forge\Table;
use Fyre\Schema\SchemaRegistry;
use Fyre\Schema\Table as SchemaTable;
use Override;

use function array_diff;
use function array_key_exists;
use function array_search;
use function array_splice;
use function array_unshift;

/**
 * MysqlTable
 */
class MysqlTable extends Table
{
    /**
     * New MysqlTable constructor.
     *
     * @param Container $container The Container.
     * @param Forge $forge The forge.
     * @param string $name The table name.
     * @param string|null $comment The table comment.
     * @param string|null $engine The table engine.
     * @param string|null $charset The table character set.
     * @param string|null $collation The table collation.
     * @param SchemaRegistry The SchemaRegistry.
     */
    public function __construct(
        Container $container,
        Forge $forge,
        SchemaRegistry $schemaRegistry,
        string $name,
        string|null $comment = null,
        protected string|null $engine = null,
        protected string|null $charset = null,
        protected string|null $collation = null,
    ) {
        parent::__construct($container, $forge, $schemaRegistry, $name, $comment);

        $this->engine ??= 'InnoDB';
        $this->charset ??= $this->forge->getConnection()->getCharset();
        $this->collation ??= $this->forge->getConnection()->getCollation();
    }

    /**
     * Add a column to the table.
     *
     * @param string $name The column name.
     * @param array $options The column options.
     * @return Table The Table.
     */
    #[Override]
    public function addColumn(string $name, array $options = []): static
    {
        $after = $options['after'] ?? null;
        $first = $options['first'] ?? false;

        unset($options['after']);
        unset($options['first']);

        $options['charset'] ??= $this->charset;
        $options['collation'] ??= $this->collation;

        parent::addColumn($name, $options);

        $this->updateColumnOrder($name, $first, $after);

        return $this;
    }

    /**
     * Change a table column.
     *
     * @param string $name The column name.
     * @param array $options The column options.
     * @return Table The Table.
     */
    #[Override]
    public function changeColumn(string $name, array $options): static
    {
        $first = $options['first'] ?? false;
        $after = $options['after'] ?? null;

        unset($options['after']);

        parent::changeColumn($name, $options);

        $this->updateColumnOrder($options['name'] ?? $name, $first, $after);

        return $this;
    }

    /**
     * Determine whether this table is equivalent to a Schema Table.
     *
     * @param SchemaTable $schemaTable The Schema Table.
     * @return bool TRUE if the tables are equivalent, otherwise FALSE.
     */
    #[Override]
    public function compare(SchemaTable $schemaTable): bool
    {
        return parent::compare($schemaTable) &&
            $this->engine === $schemaTable->getEngine() &&
            $this->charset === $schemaTable->getCharset() &&
            $this->collation === $schemaTable->getCollation();
    }

    /**
     * Get the table character set.
     *
     * @return string|null The table character set.
     */
    public function getCharset(): string|null
    {
        return $this->charset;
    }

    /**
     * Get the table collation.
     *
     * @return string|null The table collation.
     */
    public function getCollation(): string|null
    {
        return $this->collation;
    }

    /**
     * Get the table engine.
     *
     * @return string|null The table engine.
     */
    public function getEngine(): string|null
    {
        return $this->engine;
    }

    /**
     * Set the primary key.
     *
     * @param array|string $columns The columns.
     * @return Table The Table.
     */
    #[Override]
    public function setPrimaryKey(array|string $columns): static
    {
        $this->addIndex('PRIMARY', [
            'columns' => (array) $columns,
            'primary' => true,
        ]);

        return $this;
    }

    /**
     * Generate the SQL queries.
     *
     * @return array The SQL queries.
     */
    #[Override]
    public function sql(): array
    {
        $generator = $this->forge->generator();

        if (!$this->schemaTable) {
            $query = $generator->buildCreateTable($this);

            return [$query];
        }

        if ($this->dropTable) {
            $query = $generator->buildDropTable($this->name);

            return [$query];
        }

        $originalColumns = $this->schemaTable->columns()->toArray();
        $originalIndexes = $this->schemaTable->indexes()->toArray();
        $originalForeignKeys = $this->schemaTable->foreignKeys()->toArray();

        $statements = [];

        if ($this->engine !== $this->schemaTable->getEngine()) {
            $statements[] = $generator->buildTableEngine($this->engine);
        }

        if ($this->charset !== $this->schemaTable->getCharset()) {
            $statements[] = $generator->buildTableCharset($this->charset);
        }

        if ($this->collation !== $this->schemaTable->getCollation()) {
            $statements[] = $generator->buildTableCollation($this->collation);
        }

        if ($this->comment !== $this->schemaTable->getComment()) {
            $statements[] = $generator->buildTableComment($this->comment);
        }

        foreach ($originalForeignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $this->foreignKeys) && $this->foreignKeys[$name]->compare($foreignKey)) {
                continue;
            }

            $statements[] = $generator->buildDropForeignKey($name);
        }

        foreach ($originalIndexes as $name => $index) {
            if (array_key_exists($name, $originalForeignKeys)) {
                continue;
            }

            if (array_key_exists($name, $this->indexes) && $this->indexes[$name]->compare($index)) {
                continue;
            }

            if ($index->isPrimary()) {
                $statements[] = $generator->buildDropPrimaryKey();
            } else {
                $statements[] = $generator->buildDropIndex($name);
            }
        }

        $originalColumnNames = [];
        foreach ($originalColumns as $name => $column) {
            $newName = $this->renameColumns[$name] ?? $name;

            if (array_key_exists($newName, $this->columns)) {
                $originalColumnNames[] = $newName;

                continue;
            }

            $statements[] = $generator->buildDropColumn($name);
        }

        $prevColumn = null;
        $columnIndex = 0;

        foreach ($this->columns as $name => $column) {
            $originalName = array_search($name, $this->renameColumns) ?: $name;
            $oldIndex = array_search($name, $originalColumnNames);

            $options = [];

            if ($oldIndex === false || $columnIndex !== $oldIndex) {
                if ($prevColumn) {
                    $options['after'] = $prevColumn;
                    $originalColumnNames = array_diff($originalColumnNames, [$name]);
                    $prevIndex = array_search($prevColumn, $originalColumnNames);
                    array_splice($originalColumnNames, $prevIndex + 1, 0, $name);
                } else {
                    $options['first'] = true;
                    array_unshift($originalColumnNames, $name);
                }
            }

            if (!array_key_exists($originalName, $originalColumns)) {
                $statements[] = $generator->buildAddColumn($column, $options);
            } else if ($name !== $originalName || $columnIndex !== $oldIndex || !$column->compare($originalColumns[$originalName])) {
                $options['name'] = $originalName;
                $options['forceComment'] = $column->getComment() !== $originalColumns[$originalName]->getComment();
                $statements[] = $generator->buildChangeColumn($column, $options);
            }

            $prevColumn = $name;
            $columnIndex++;
        }

        foreach ($this->indexes as $name => $index) {
            if (array_key_exists($name, $this->foreignKeys)) {
                continue;
            }

            if (array_key_exists($name, $originalIndexes) && $index->compare($originalIndexes[$name])) {
                continue;
            }

            $statements[] = $generator->buildAddIndex($index);
        }

        foreach ($this->foreignKeys as $name => $foreignKey) {
            if (array_key_exists($name, $originalForeignKeys) && $foreignKey->compare($originalForeignKeys[$name])) {
                continue;
            }

            $statements[] = $generator->buildAddForeignKey($foreignKey);
        }

        if ($this->newName && $this->newName !== $this->name) {
            $statements[] = $generator->buildRenameTable($this->newName);
        }

        if ($statements === []) {
            return [];
        }

        $query = $generator->buildAlterTable($this->name, $statements);

        return [$query];
    }

    /**
     * Get the table data as an array.
     *
     * @return array The table data.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'engine' => $this->engine,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'comment' => $this->comment,
        ];
    }

    /**
     * Build a Column.
     *
     * @param string $name The column name.
     * @param array $data The column data.
     * @return MysqlColumn The Column.
     */
    #[Override]
    protected function buildColumn(string $name, array $data): MysqlColumn
    {
        return $this->container->build(MysqlColumn::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Build an Index.
     *
     * @param string $name The index key name.
     * @param array $data The index key data.
     * @return MysqlIndex The Index.
     */
    #[Override]
    protected function buildIndex(string $name, array $data): MysqlIndex
    {
        return $this->container->build(MysqlIndex::class, [
            'table' => $this,
            'name' => $name,
            ...$data,
        ]);
    }

    /**
     * Reload table data from the schema.
     *
     * @param bool $forceReset Whether to forcefully reload the schema data.
     */
    #[Override]
    protected function reloadSchema(bool $forceReset = false): void
    {
        parent::reloadSchema();

        if ($forceReset) {
            $this->engine = $this->schemaTable->getEngine();
            $this->charset = $this->schemaTable->getCharset();
            $this->collation = $this->schemaTable->getCollation();
        } else {
            $this->engine ??= $this->schemaTable->getEngine();
            $this->charset ??= $this->schemaTable->getCharset();
            $this->collation ??= $this->schemaTable->getCollation();
        }
    }
}
