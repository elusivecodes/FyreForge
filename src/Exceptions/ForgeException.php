<?php
declare(strict_types=1);

namespace Fyre\Forge\Exceptions;

use
    RunTimeException;

/**
 * ForgeException
 */
class ForgeException extends RunTimeException
{

    public static function forExistingColumn(string $column)
    {
        return new static('Table column already exists: '.$column);
    }

    public static function forExistingForeignKey(string $foreignKey)
    {
        return new static('Table foreign key already exists: '.$foreignKey);
    }

    public static function forExistingIndex(string $index)
    {
        return new static('Table index already exists: '.$index);
    }

    public static function forMissingColumn(string $column)
    {
        return new static('Table column does not exist: '.$column);
    }

    public static function forMissingForeignKey(string $foreignKey)
    {
        return new static('Table foreign key does not exist: '.$foreignKey);
    }

    public static function forMissingHandler(string $name)
    {
        return new static('Missing handler for connection handler: '.$name);
    }

    public static function forMissingIndex(string $index)
    {
        return new static('Table index does not exist: '.$index);
    }

    public static function forMissingTable(string $table)
    {
        return new static('Table does not exist: '.$table);
    }

}
