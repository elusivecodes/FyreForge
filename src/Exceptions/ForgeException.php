<?php
declare(strict_types=1);

namespace Fyre\Forge\Exceptions;

use RunTimeException;

/**
 * ForgeException
 */
class ForgeException extends RunTimeException
{
    public static function forExistingColumn(string $column): static
    {
        return new static('Table column already exists: '.$column);
    }

    public static function forExistingForeignKey(string $foreignKey): static
    {
        return new static('Table foreign key already exists: '.$foreignKey);
    }

    public static function forExistingIndex(string $index): static
    {
        return new static('Table index already exists: '.$index);
    }

    public static function forInvalidConstraint(string $index): static
    {
        return new static('Constraint not valid: '.$index);
    }

    public static function forInvalidIndexOnTableCreation(string $index): static
    {
        return new static('Indexes cannot be added during table creation: '.$index);
    }

    public static function forInvalidIndexType(string $type): static
    {
        return new static('Index type not valid: '.$type);
    }

    public static function forMissingColumn(string $column): static
    {
        return new static('Table column does not exist: '.$column);
    }

    public static function forMissingForeignKey(string $foreignKey): static
    {
        return new static('Table foreign key does not exist: '.$foreignKey);
    }

    public static function forMissingHandler(string $name): static
    {
        return new static('Missing handler for connection handler: '.$name);
    }

    public static function forMissingIndex(string $index): static
    {
        return new static('Table index does not exist: '.$index);
    }

    public static function forMissingTable(string $table): static
    {
        return new static('Table does not exist: '.$table);
    }
}
