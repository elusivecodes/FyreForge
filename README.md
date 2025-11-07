# FyreForge

**FyreForge** is a free, open-source database forge library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Forges](#forges)
    - [MySQL Forges](#mysql-forges)
    - [Postgres Forges](#postgres-forges)
    - [Sqlite Forges](#sqlite-forges)
- [Table](#tables)
    - [MySQL Tables](#mysql-tables)
    - [Postgres Tables](#postgres-tables)
    - [Sqlite Tables](#sqlite-tables)
- [Columns](#columns)
    - [MySQL Columns](#mysql-columns)
- [Indexes](#indexes)
- [Foreign Keys](#foreign-keys)



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

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).

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

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.

```php
$forge->addColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Additional column options may be available depending on the connection handler.

**Add Foreign Key**

Add a foreign key to a table.

- `$tableName` is a string representing the table name.
- `$foreignKeyName` is a string representing the foreign key name.
- `$options` is an array containing the foreign key options.
    - `columns` is a string or array containing the columns to use for the foreign key, and will default to the foreign key name.
    - `referencedTable` is a string representing the referenced table to use.
    - `referencedColumns` is a string or array containing the columns to use in the referenced table.
    - `onUpdate` is a string containing the ON UPDATE operation, and will default to *null*.
    - `onDelete` is a string containing the ON DELETE operation, and will default to *null*.

```php
$forge->addForeignKey($tableName, $foreignKeyName, $options);
```

Foreign keys cannot be added to an existing Sqlite table.

**Add Index**

Add an index to a table.

- `$tableName` is a string representing the table name.
- `$indexName` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$forge->addIndex($tableName, $indexName, $options);
```

Additional index options may be available depending on the connection handler.

Primary keys cannot be added to an existing Sqlite table.

**Alter Table**

Alter a table.

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.

```php
$forge->alterTable($tableName, $options);
```

Additional table options may be available depending on the connection handler.

**Build**

Build a [*Table*](#tables).

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.

```php
$table = $forge->build($tableName, $options);
```

[*Table*](#tables) dependencies will be resolved automatically from the Container.

Additional table options may be available depending on the connection handler.

**Create Table**

Create a new table.

- `$tableName` is a string representing the table name.
- `$columns` is an array containing the column definitions.
- `$indexes` is an array containing the index definitions.
- `$foreignKeys` is an array containing the foreign key definitions.
- `$options` is an array containing the schema options.

```php
$forge->createTable($tableName, $columns, $indexes, $foreignKeys);
```

Additional table options may be available depending on the connection handler.

**Drop Column**

Drop a column from a table.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.

```php
$forge->dropColumn($tableName, $columnName);
```

**Drop Foreign Key**

Drop a foreign key from a table.

- `$tableName` is a string representing the table name.
- `$foreignKeyName` is a string representing the foreign key name.

```php
$forge->dropForeignKey($tableName, $foreignKeyName);
```

Foreign keys cannot be dropped from an existing Sqlite table.

**Drop Index**

Drop an index from a table.

- `$tableName` is a string representing the table name.
- `$indexName` is a string representing the index name.

```php
$forge->dropIndex($tableName, $indexName);
```

Primary keys cannot be dropped from an existing Sqlite table.

**Drop Table**

Drop a table.

- `$tableName` is a string representing the table name.

```php
$forge->dropTable($tableName, $options);
```

**Get Connection**

Get the [*Connection*](https://github.com/elusivecodes/FyreDB#connections).

```php
$connection = $forge->getConnection();
```

**Rename Column**

Rename a column.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$newColumnName` is a string representing the new column name.

```php
$forge->renameColumn($tableName, $columnName, $newColumnName);
```

**Rename Table**

Rename a table.

- `$tableName` is a string representing the table name.
- `$newTableName` is a string representing the new table name.

```php
$forge->renameTable($tableName, $newTableName);
```

### MySQL Forges

The [*MySQL*](https://github.com/elusivecodes/FyreDB#MySQL) Forge extends the *Forge* class and provides additional methods and options specific to MySQL databases.

**Add Column**

Add a column to a table.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
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
$forge->addColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Add Index**

Add an index to a table.

- `$tableName` is a string representing the table name.
- `$indexName` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `type` is a string representing the index type, and will default to "*BTREE*".
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$forge->addIndex($tableName, $indexName, $options);
```

**Alter Table**

Alter a table.

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".

```php
$forge->alterTable($tableName, $options);
```

**Build**

Build a [*Table*](#tables).

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".

```php
$table = $forge->build($tableName, $options);
```

[*Table*](#tables) dependencies will be resolved automatically from the Container.

**Change Column**

Change a table column.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name.
    - `type` is a string representing the column type.
    - `length` is a number representing the column length.
    - `precision` is a number representing the column precision.
    - `values` is an array containing the enum/set values.
    - `nullable` is a boolean indicating whether the column is nullable.
    - `unsigned` is a boolean indicating whether the column is unsigned.
    - `default` is a string representing the column default value.
    - `charset` is a string representing the column character set.
    - `collation` is a string representing the column collation.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column.
    - `comment` is a string representing the column comment.
    - `after` is a string representing the column to add this column after.
    - `first` is a boolean indicating whether to add this column first in the table.

```php
$forge->changeColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Unspecified options will default to the current value.

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
- `$indexes` is an array containing the index definitions.
- `$foreignKeys` is an array containing the foreign key definitions.
- `$options` is an array containing the schema options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".

```php
$forge->createTable($table, $columns, $indexes, $foreignKeys, $options);
```

**Drop Primary Key**

Drop a primary key from a table.

- `$tableName` is a string representing the table name.

```php
$forge->dropPrimaryKey($tableName);
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

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.
    - `comment` is a string representing the column comment, and will default to "".

```php
$forge->addColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Alter Table**

Alter a table.

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.
    - `comment` is a string representing the table comment, and will default to "".

```php
$forge->alterTable($tableName, $options);
```

**Build**

Build a [*Table*](#tables).

- `$tableName` is a string representing the table name.
- `$options` is an array containing the table options.
    - `comment` is a string representing the table comment, and will default to "".

```php
$table = $forge->build($tableName, $options);
```

[*Table*](#tables) dependencies will be resolved automatically from the Container.

**Change Column**

Change a table column.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name.
    - `type` is a string representing the column type.
    - `length` is a number representing the column length.
    - `precision` is a number representing the column precision.
    - `nullable` is a boolean indicating whether the column is nullable.
    - `default` is a string representing the column default value.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column.
    - `comment` is a string representing the column comment.

```php
$forge->changeColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Unspecified options will default to the current value.

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
- `$indexes` is an array containing the index definitions.
- `$foreignKeys` is an array containing the foreign key definitions.
- `$options` is an array containing the schema options.
    - `comment` is a string representing the table comment, and will default to "".

```php
$forge->createTable($tableName, $columns, $indexes, $foreignKeys, $options);
```

**Drop Primary Key**

Drop a primary key from a table.

- `$tableName` is a string representing the table name.

```php
$forge->dropPrimaryKey($tableName);
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

**Add Column**

Add a column to a table.

- `$tableName` is a string representing the table name.
- `$columnName` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.

```php
$forge->addColumn($tableName, $columnName, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.


## Tables

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
$table->addColumn($column, $options);
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
    - `onUpdate` is a string containing the ON UPDATE operation, and will default to *null*.
    - `onDelete` is a string containing the ON DELETE operation, and will default to *null*.

```php
$table->addForeignKey($foreignKey, $options);
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
$table->addIndex($index, $options);
```

Additional index options may be available depending on the connection handler.

Primary keys cannot be added to an existing Sqlite table.

**Change Column**

Change a table column.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.

```php
$table->changeColumn($column, $options);
```

Additional column options may be available depending on the connection handler.

Column definitions can not be modified for an existing Sqlite table.

**Clear**

Clear the column and index data.

```php
$table->clear();
```

**Column**

Get a [*Column*](#columns).

- `$name` is a string representing the column name.

```php
$column = $table->column($name);
```

**Column Names**

Get the names of all table columns.

```php
$columnNames = $table->columnNames();
```

**Columns**

Get all table [columns](#columns).

```php
$columns = $table->columns();
```

**Drop**

Drop the table.

```php
$table->drop();
```

**Drop Column**

Drop a column from the table.

- `$column` is a string representing the column name.

```php
$table->dropColumn($column);
```

**Drop Foreign Key**

Drop a foreign key from the table.

- `$foreignKey` is a string representing the foreign key name.

```php
$table->dropForeignKey($foreignKey);
```

Foreign keys cannot be dropped from an existing Sqlite table.

**Drop Index**

Drop an index from the table.

- `$index` is a string representing the index name.

```php
$table->dropIndex($index);
```

Primary keys cannot be dropped from an existing Sqlite table.

**Execute**

Generate and execute the SQL queries.

```php
$table->execute();
```

**Foreign Key**

Get a table [*ForeignKey*](#foreign-keys).

- `$name` is a string representing the foreign key name.

```php
$foreignKey = $table->foreignKey($name);
```

**Foreign Keys**

Get all table [foreign keys](#foreign-keys).

```php
$foreignKeys = $table->foreignKeys();
```

**Get Comment**

Get the table comment.

```php
$comment = $table->getComment();
```

**Get Forge**

Get the [*Forge*](#forges).

```php
$forge = $table->getForge();
```

**Get Name**

Get the table name.

```php
$name = $table->getName();
```

**Has Column**

Determine whether the table has a column.

- `$name` is a string representing the column name.

```php
$hasColumn = $table->hasColumn($name);
```

**Has Foreign Key**

Determine whether the table has a foreign key.

- `$name` is a string representing the foreign key name.

```php
$hasForeignKey = $table->hasForeignKey($name);
```

**Has Index**

Determine whether the table has an index.

- `$name` is a string representing the index name.

```php
$hasIndex = $table->hasIndex($name);
```

**Index**

Get a table [*Index*](#indexes).

- `$name` is a string representing the index name.

```php
$index = $table->index($name);
```

**Indexes**

Get all table [indexes](#indexes).

```php
$indexes = $table->indexes();
```

**Rename**

Rename the table.

- `$table` is a string representing the new table name.

```php
$table->rename($table);
```

**Set Primary Key**

Set the primary key.

- `$columns` is a string or array containing the columns to use for the primary key.

```php
$table->setPrimaryKey($columns);
```

Primary keys cannot be added to an existing Sqlite table.

**SQL**

Generate the SQL queries.

```php
$queries = $table->sql();
```

**To Array**

Get the table data as an array.

```php
$data = $table->toArray();
```

### MySQL Tables

**Add Column**

Add a column to the table.

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
$table->addColumn($column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Add Index**

Add an index to the table.

- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `type` is a string representing the index type, and will default to "*BTREE*".
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$table->addIndex($index, $options);
```

**Change Column**

Change a table column.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name.
    - `type` is a string representing the column type.
    - `length` is a number representing the column length.
    - `precision` is a number representing the column precision.
    - `values` is an array containing the enum/set values.
    - `nullable` is a boolean indicating whether the column is nullable.
    - `unsigned` is a boolean indicating whether the column is unsigned.
    - `default` is a string representing the column default value.
    - `charset` is a string representing the column character set.
    - `collation` is a string representing the column collation.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column.
    - `comment` is a string representing the column comment.
    - `after` is a string representing the column to add this column after.
    - `first` is a boolean indicating whether to add this column first in the table.

```php
$table->changeColumn($column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Unspecified options will default to the current value.

**Get Charset**

Get the table character set.

```php
$charset = $table->getCharset();
```

**Get Collation**

Get the table collation.

```php
$collation = $table->getCollation();
```

**Get Engine**

Get the table engine.

```php
$engine = $table->getEngine();
```

### Postgres Tables

**Add Column**

Add a column to the table.

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
$table->addColumn($column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

**Add Index**

Add an index to the table.

- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `type` is a string representing the index type, and will default to "*BTREE*".
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.
    - `primary` is a boolean indicating whether the index is a primary key, and will default to *false*.

```php
$table->addIndex($index, $options);
```

**Change Column**

Change a table column.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name.
    - `type` is a string representing the column type.
    - `length` is a number representing the column length.
    - `precision` is a number representing the column precision.
    - `nullable` is a boolean indicating whether the column is nullable.
    - `default` is a string representing the column default value.
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column.
    - `comment` is a string representing the column comment.

```php
$table->changeColumn($column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.

Unspecified options will default to the current value.

### Sqlite Tables

**Add Column**

Add a column to the table.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to `StringType::class`.
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `autoIncrement` is a boolean indicating whether the column is an an auto incrementing column, and will default to *false*.

```php
$table->addColumn($column, $options);
```

You can also specify a [*Type*](https://github.com/elusivecodes/FyreTypeParser#types) class name as the `type`, which will be automatically mapped to the correct type.


## Columns

**Get Comment**

Get the column comment.

```php
$comment = $column->getComment();
```

**Get Default**

Get the column default value.

```php
$default = $column->getDefault();
```

**Get Length**

Get the column length.

```php
$length = $column->getLength();
```

**Get Name**

Get the column name.

```php
$name = $column->getName();
```

**Get Precision**

Get the column precision.

```php
$precision = $column->getPrecision();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $column->getTable();
```

**Get Type**

Get the column type.

```php
$type = $column->getType();
```

**Is Auto Increment**

Determine whether the column is an auto increment column.

```php
$isAutoIncrement = $column->isAutoIncrement();
```

**Is Nullable**

Determine whether the column is nullable.

```php
$isNullable = $column->isNullable();
```

**Is Unsigned**

Determine whether the column is unsigned.

```php
$isUnsigned = $column->isUnsigned();
```

**To Array**

Get the column data as an array.

```php
$data = $column->toArray();
```

### MySQL Columns

**Get Charset**

Get the column character set.

```php
$charset = $column->getCharset();
```

**Get Collation**

Get the column collation.

```php
$collation = $column->getCollation();
```

**Get Values**

Get the column enum values.

```php
$values = $column->getValues();
```


## Indexes

**Get Columns**

Get the column names.

```php
$columns = $index->getColumns();
```

**Get Name**

Get the index name.

```php
$name = $index->getName();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $index->getTable();
```

**Get Type**

Get the index type.

```php
$type = $index->getType();
```

**Is Primary**

Determine whether the index is primary.

```php
$isPrimary = $index->isPrimary();
```

**Is Unique**

Determine whether the index is unique.

```php
$isUnique = $index->isUnique();
```

**To Array**

Get the index data as an array.

```php
$data = $index->toArray();
```


## Foreign Keys

**Get Columns**

Get the column names.

```php
$columns = $foreignKey->getColumns();
```

**Get Name**

```php
$name = $foreignKey->getName();
```

**Get On Delete**

Get the delete action.

```php
$onDelete = $foreignKey->getOnDelete();
```

**Get On Update**

Get the update action.

```php
$onUpdate = $foreignKey->getOnUpdate();
```
**Get Referenced Columns**

Get the referenced column names.

```php
$referencedColumn = $foreignKey->getReferencedColumns();
```

**Get Referenced Table**

Get the referenced table name.

```php
$referencedTable = $foreignKey->getReferencedTable();
```

**Get Table**

Get the [*Table*](#tables).

```php
$table = $foreignKey->getTable();
```

**To Array**

Get the foreign key data as an array.

```php
$data = $foreignKey->toArray();
```
