# FyreForge

**FyreForge** is a free, database forge library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Forge Registry](#forge-registry)
- [Forges](#forges)
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


## Forge Registry

**Get Forge**

Get the *Forge* for a *Connection*.

- `$connection` is a [*Connection*](https://github.com/elusivecodes/FyreDB).

```php
$forge = ForgeRegistry::getForge($connection);
```

**Set Handler**

Set a *Forge* handler for a *Connection* class.

- `$connectionClass` is a string representing the *Connection* class name.
- `$schemaClass` is a string representing the *Schema* class name.

```php
ForgeRegistry::setHandler($connectionClass, $schemaClass);
```


## Forges

This class extends the [*Schema*](https://github.com/elusivecodes/FyreSchema#schemas) class.

**Add Column**

Add a column to a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to "*varchar*".
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `extra` is a string representing the column extra definition, and will default to "".
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$forge->addColumn($table, $column, $options);
```

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
    - `clean` is a boolean indicating whether to create a clean forge, and will default to *false*.

```php
$tableForge = $forge->build($table, $options);
```

**Change Column**

Change a table column.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.
    - `type` is a string representing the column type, and will default to "*varchar*".
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `extra` is a string representing the column extra definition, and will default to "".
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$forge->changeColumn($table, $column, $options);
```

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
- `$options` is an array containing the schema options.
    - `engine` is a string representing the table engine, and will default to "*InnoDB*".
    - `charset` is a string representing the table character set, and will default to the connection character set.
    - `collation` is a string representing the table collation, and will default to the connection collation.
    - `comment` is a string representing the table comment, and will default to "".
    - `ifNotExists` is a boolean indicating whether to use an `IF NOT EXISTS` clause, and will default to *false*.

```php
$forge->createTable($table, $options);
```

**Drop Column**

Drop a column from a table.

- `$table` is a string representing the table name.
- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropColumn($table, $column, $options);
```

**Drop Foreign Key**

Drop a foreign key from a table.

- `$table` is a string representing the table name.
- `$foreignKey` is a string representing the foreign key name.

```php
$forge->dropForeignKey($table, $foreignKey);
```

**Drop Index**

Drop an index from a table.

- `$table` is a string representing the table name.
- `$index` is a string representing the index name.

```php
$forge->dropIndex($table, $index);
```

**Drop Schema**

Drop a schema.

- `$schema` is a string representing the schema name.
- `$options` is an array containing the schema options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropSchema($schema, $options);
```

**Drop Table**

Drop a table.

- `$table` is a string representing the table name.
- `$options` is an array containing the table options.
    - `ifExists` is a boolean indicating whether to use an `IF EXISTS` clause, and will default to *false*.

```php
$forge->dropTable($table, $options);
```

**Rename Table**

Rename a table.

- `$table` is a string representing the table name.
- `$newTable` is a string representing the new table name.

```php
$forge->renameTable($table, $newTable);
```


## Table Forges

This class extends the [*TableSchema*](https://github.com/elusivecodes/FyreSchema#table-schemas) class.

**Add Column**

Add a column to the table.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `type` is a string representing the column type, and will default to "*varchar*".
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `extra` is a string representing the column extra definition, and will default to "".
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$tableForge->addColumn($column, $options);
```

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

**Add Index**

Add an index to the table.

- `$index` is a string representing the index name.
- `$options` is an array containing the index options.
    - `columns` is a string or array containing the columns to use for the index, and will default to the index name.
    - `type` is a string representing the index type, and will default to "*BTREE*".
    - `unique` is a boolean indicating whether the index must be unique, and will default to *false*.

```php
$tableForge->addIndex($index, $options);
```

**Change Column**

Change a table column.

- `$column` is a string representing the column name.
- `$options` is an array containing the column options.
    - `name` is a string representing the new column name, and will default to the column name.
    - `type` is a string representing the column type, and will default to "*varchar*".
    - `length` is a number representing the column length, and will default to the type default.
    - `precision` is a number representing the column precision, and will default to the type default.
    - `values` is an array containing the enum/set values, and will default to *null*.
    - `nullable` is a boolean indicating whether the column is nullable, and will default to *false*.
    - `unsigned` is a boolean indicating whether the column is unsigned, and will default to *false*.
    - `default` is a string representing the column default value, and will default to *null* (no default).
    - `charset` is a string representing the column character set, and will default to the connection character set.
    - `collation` is a string representing the column collation, and will default to the connection collation.
    - `extra` is a string representing the column extra definition, and will default to "".
    - `comment` is a string representing the column comment, and will default to "".
    - `after` is a string representing the column to add this column after, and will default to *null*.
    - `first` is a boolean indicating whether to add this column first in the table, and will default to *false*.

```php
$tableForge->changeColumn($column, $options);
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

**Drop Index**

Drop an index from the table.

- `$index` is a string representing the index name.

```php
$tableForge->dropIndex($index);
```

**Execute**

Generate and execute the SQL queries.

```php
$tableForge->execute();
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

**SQL**

Generate the SQL queries.

```php
$queries = $tableForge->sql();
```