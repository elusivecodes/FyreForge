<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Forge\Column;
use Fyre\Forge\ForeignKey;
use Fyre\Forge\Forge;
use Fyre\Forge\ForgeRegistry;
use Fyre\Forge\Index;
use Fyre\Forge\Table;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

final class ForgeRegistryTest extends TestCase
{
    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(ForgeRegistry::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Forge::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Table::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Column::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Index::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(ForeignKey::class)
        );
    }
}
