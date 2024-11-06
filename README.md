# FyreForge

**FyreForge** is a free, open-source database forge library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Forges](#forges)
    - [MySQL](#mysql)
    - [Postgres](#postgres)
    - [Sqlite](#sqlite)
- [Table Forges](#table-forges)



## Installation

**Using Composer**

```
composer require fyre/forge
```

In PHP:

```php
use Fyre\Forge\ForgeRegistry;
```


## Basic Usage

- `$container` is a  [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$forgeRegistry = new ForgeRegistry($container);
```

**Autoloading**

It is recommended to bind the *ForgeRegistry* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(ForgeRegistry::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$forgeRegistry = $container->use(ForgeRegistry::class);
```


## Methods

**Map**

Map a [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class to a [*Forge*](#forges) handler.

- `$connectionClass` is a string representing the [*Connection*](https://github.com/elusivecodes/FyreDB#connections) class name.
- `$forgeClass` is a string representing the [*Forge*](#forges) class name.

```php
$forgeRegistry->map($connectionClass, $forgeClass);
```

**Use**

Load the shared [*Forge*](#forges) for a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

- `$connection` is a [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$forge = $forgeRegistry->use($connection);
```

[*Forge*](#forges) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).


## Forges

**Add Column**

Add a column to a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.

```php
$forge->addColumn($table, $column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Additional column options may be available depending on the connection handler.

**Add Index**

Add an index to a table.

- `$table` is a string representing the table name.
- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$forge->addIndex($table, $index, $options);
```

Additional index options may be available depending on the connection handler.

**Build**

Build a [*TableForge*](#table-forges).

- `$table` is a string representing the table name.

```php
$tableForge = $forge->build($table);
```

[*TableForge*](#table-forges) dependencies will be resolved automatically from the Container.

Additional table options may be available depending on the connection handler.

**Create Table**

Create a new table.

- `$table` is a string representing the table name.
- `$columns` is an array containing the column definitions.
- `$options` is an array containing the schema options.
    - `indexes` is an array containing the index definitions.
    - `foreignKeys` is an array containing the foreign key definitions.
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createTable($table, $columns, $options);
```

Additional table options may be available depending on the connection handler.

**Drop Column**

Drop a column from a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropColumn($table, $column, $options);
```

**Drop Index**

Drop an index from a table.

- `$table` is a string representing the table name.
- `$index` is a string representing the index name.

```php
$forge->dropIndex($table, $index);
```

**Drop Table**

Drop a table.

- `$table` is a string representing the table name.
- `$options` is an array containing the table options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropTable($table, $options);
```

**Get Connection**

Get the [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$connection = $forge->getConnection();
```

**Rename Column**

Rename a column.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$newColumn` is a string representing the new column name.

```php
$forge->renameColumn($table, $column, $newColumn);
```

**Rename Table**

Rename a table.

- `$table` is a string representing the table name.
- `$newTable` is a string representing the new table name.

```php
$forge->renameTable($table, $newTable);
```

### MySQL

The [*MySQL*](https://github.com/elusivecodes/FyreDB#MySQL) Forge extends the *Forge* class and provides additional methods and options specific to MySQL databases.

**Add Column**

Add a column to a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$forge->addColumn($table, $column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Add Foreign Key**

Add a foreign key to a table.

- `$table` is a string representing the table name.
- `$foreignKey` is a string representing the foreign key name.
- `$options` is an array containing the foreign key options.
    - `columns` is a string or array containing the columns to use for the foreign key, and will default to the foreign key name.
    - `referencedTable` is a string representing the referenced table to use.
    - `referencedColumns` is a string or array containing the columns to use in the referenced table.
    - `update` is a string containing the ON UPDATE operation, and will default to "".
    - `delete` is a string containing the ON DELETE operation, and will default to "".

```php
$forge->addForeignKey($table, $foreignKey, $options);
```

**Add Index**

Add an index to a table.

- `$table` is a string representing the table name.
- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `type` is a string representing the index type, and will default to "*BTREE*".
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$forge->addIndex($table, $index, $options);
```

**Alter Table**

Alter a table.

- `$table` is a string representing the table name.
- `$options` is an array containing the table options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".

```php
$forge->alterTable($table, $options);
```

**Build**

Build a [*TableForge*](#table-forges).

- `$table` is a string representing the table name.
- `$options` is an array containing the table options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".

```php
$tableForge = $forge->build($table, $options);
```

[*TableForge*](#table-forges) dependencies will be resolved automatically from the Container.

**Change Column**

Change a table column.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$forge->changeColumn($table, $column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Create Schema**

Create a new schema.

- `$schema` is a string representing the schema name.
- `$options` is an array containing the schema options.
    - `charset` is a string representing the schema character set, and will default to the connection character set.
    - `collation` is a string representing the schema collation, and will default to the connection collation.
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createSchema($schema, $options);
```

**Create Table**

Create a new table.

- `$table` is a string representing the table name.
- `$columns` is an array containing the column definitions.
- `$options` is an array containing the schema options.
    - `indexes` is an array containing the index definitions.
    - `foreignKeys` is an array containing the foreign key definitions.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createTable($table, $columns, $options);
```

**Drop Foreign Key**

Drop a foreign key from a table.

- `$table` is a string representing the table name.
- `$foreignKey` is a string representing the foreign key name.

```php
$forge->dropForeignKey($table, $foreignKey);
```

**Drop Primary Key**

Drop a primary key from a table.

- `$table` is a string representing the table name.

```php
$forge->dropPrimaryKey();
```

**Drop Schema**

Drop a schema.

- `$schema` is a string representing the schema name.
- `$options` is an array containing the schema options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropSchema($schema, $options);
```

### Postgres

The [*Postgres*](https://github.com/elusivecodes/FyreDB#Postgres) Forge extends the *Forge* class and provides additional methods and options specific to Postgres databases.

**Add Column**

Add a column to a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.
    - `comment` is a string representing the column comment, and will default to "".

```php
$forge->addColumn($table, $column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Add Foreign Key**

Add a foreign key to a table.

- `$table` is a string representing the table name.
- `$foreignKey` is a string representing the foreign key name.
- `$options` is an array containing the foreign key options.
    - `columns` is a string or array containing the columns to use for the foreign key, and will default to the foreign key name.
    - `referencedTable` is a string representing the referenced table to use.
    - `referencedColumns` is a string or array containing the columns to use in the referenced table.
    - `update` is a string containing the ON UPDATE operation, and will default to "".
    - `delete` is a string containing the ON DELETE operation, and will default to "".

```php
$forge->addForeignKey($table, $foreignKey, $options);
```

**Alter Column Auto Increment**

Alter a column's auto increment.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$autoIncrement` is a boolean indicating whether the column is an an auto incrementing column.

```php
$forge->alterColumnAutoIncrement($table, $column, $autoIncrement);
```

**Alter Column Default**

Alter a column's default value.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$default` is a string representing the column default value.

```php
$forge->alterColumnDefault($table, $column, $default);
```

**Alter Column Nullable**

Alter whether a column is nullable.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$nullable` is a boolean indicating whether the column is nullable, and will default to *false*.

```php
$forge->alterColumnNullable($table, $column, $nullable);
```

**Alter Column Type**

Alter a column's type.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.

```php
$forge->alterColumnType($table, $column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Build**

Build a [*TableForge*](#table-forges).

- `$table` is a string representing the table name.
- `$options` is an array containing the table options.
    - `comment` is a string representing the table comment, and will default to "".

```php
$tableForge = $forge->build($table, $options);
```

[*TableForge*](#table-forges) dependencies will be resolved automatically from the Container.

**Comment On Column**

Set the comment for a column.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$comment` is a string representing the table comment.

```php
$forge->commentOnTable($table, $column, $comment);
```

**Comment On Table**

Set the comment for a table.

- `$table` is a string representing the table name.
- `$comment` is a string representing the table comment.

```php
$forge->commentOnTable($table, $comment);
```

**Create Schema**

Create a new schema.

- `$schema` is a string representing the schema name.
- `$options` is an array containing the schema options.
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createSchema($schema, $options);
```

**Create Table**

Create a new table.

- `$table` is a string representing the table name.
- `$columns` is an array containing the column definitions.
- `$options` is an array containing the schema options.
    - `indexes` is an array containing the index definitions.
    - `foreignKeys` is an array containing the foreign key definitions.
    - `comment` is a string representing the table comment, and will default to "".
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createTable($table, $columns, $options);
```

**Drop Constraint**

Drop a constraint from a table.

- `$table` is a string representing the table name.
- `$index` is a string representing the index name.

```php
$forge->dropConstraint($table, $index);
```

**Drop Foreign Key**

Drop a foreign key from a table.

- `$table` is a string representing the table name.
- `$foreignKey` is a string representing the foreign key name.

```php
$forge->dropForeignKey($table, $foreignKey);
```

**Drop Primary Key**

Drop a primary key from a table.

- `$table` is a string representing the table name.

```php
$forge->dropPrimaryKey();
```

**Drop Schema**

Drop a schema.

- `$schema` is a string representing the schema name.
- `$options` is an array containing the schema options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropSchema($schema, $options);
```

### Sqlite

The [*Sqlite*](https://github.com/elusivecodes/FyreDB#Sqlite) Forge extends the *Forge* class.


## Table Forges

**Add Column**

Add a column to the table.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.

```php
$tableForge->addColumn($column, $options);
```
You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Additional column options may be available depending on the connection handler.

**Add Foreign Key**

Add a foreign key to the table.

- `$foreignKey` is a string representing the foreign key name.
- `$options` is an array containing the foreign key options.
    - `columns` is a string or array containing the columns to use for the foreign key, and will default to the foreign key name.
    - `referencedTable` is a string representing the referenced table to use.
    - `referencedColumns` is a string or array containing the columns to use in the referenced table.
    - `update` is a string containing the ON UPDATE operation, and will default to "".
    - `delete` is a string containing the ON DELETE operation, and will default to "".

```php
$tableForge->addForeignKey($foreignKey, $options);
```

Foreign keys cannot be added to an existing Sqlite table.

**Add Index**

Add an index to the table.

- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$tableForge->addIndex($index, $options);
```

Additional index options may be available depending on the connection handler.

Primary keys cannot be added to an existing Sqlite table.

**Change Column**

Change a table column.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.

```php
$tableForge->changeColumn($column, $options);
```

Additional column options may be available depending on the connection handler.

Column definitions can not be modified for an existing Sqlite table.

**Clear**

Clear the column and index data.

```php
$tableForge->clear();
```

**Column**

Get the data for a table column.

- `$name` is a string representing the column name.

```php
$column = $tableForge->column($name);
```

**Column Names**

Get the names of all table columns.

```php
$columnNames = $tableForge->columnNames();
```

**Columns**

Get the data for all table columns.

```php
$columns = $tableForge->columns();
```

**Drop**

Drop the table.

```php
$tableForge->drop();
```

**Drop Column**

Drop a column from the table.

- `$column` is a string representing the column name.

```php
$tableForge->dropColumn($column);
```

**Drop Foreign Key**

Drop a foreign key from the table.

- `$foreignKey` is a string representing the foreign key name.

```php
$tableForge->dropForeignKey($foreignKey);
```

Foreign keys cannot be dropped from an existing Sqlite table.

**Drop Index**

Drop an index from the table.

- `$index` is a string representing the index name.

```php
$tableForge->dropIndex($index);
```

Primary and unique keys cannot be dropped from an existing Sqlite table.

**Execute**

Generate and execute the SQL queries.

```php
$tableForge->execute();
```

**Foreign Key**

Get the data for a table foreign key.

- `$name` is a string representing the foreign key name.

```php
$foreignKey = $tableForge->foreignKey($name);
```

**Foreign Keys**

Get the data for all table foreign keys.

```php
$foreignKeys = $tableForge->foreignKeys();
```

**Get Forge**

Get the [*Forge*](#forges).

```php
$forge = $tableForge->getForge();
```

**Get Table Name**

Get the table name.

```php
$tableName = $tableForge->getTableName();
```

**Has Column**

Determine if the table has a column.

- `$name` is a string representing the column name.

```php
$hasColumn = $tableForge->hasColumn($name);
```

**Has Foreign Key**

Determine if the table has a foreign key.

- `$name` is a string representing the foreign key name.

```php
$hasForeignKey = $tableForge->hasForeignKey($name);
```

**Has Index**

Determine if the table has an index.

- `$name` is a string representing the index name.

```php
$hasIndex = $tableForge->hasIndex($name);
```

**Index**

Get the data for a table index.

- `$name` is a string representing the index name.

```php
$index = $tableForge->index($name);
```

**Indexes**

Get the data for all table indexes.

```php
$indexes = $tableForge->indexes();
```

**Rename**

Rename the table.

- `$table` is a string representing the new table name.

```php
$tableForge->rename($table);
```

**Set Primary Key**

Set the primary key.

- `$columns` is a string or array containing the columns to use for the primary key.

```php
$tableForge->setPrimaryKey($columns);
```

Primary keys cannot be added to an existing Sqlite table.

**SQL**

Generate the SQL queries.

```php
$queries = $tableForge->sql();
```