<?php
declare(strict_types=1);

namespace Tests\Forge;

trait DropSchemaTest
{

    public function testDropSchema(): void
    {
        $this->forge->createSchema('other');

        $this->forge->dropSchema('other');

        $this->assertCount(
            0,
            $this->db->builder()
                ->select('*')
                ->table('INFORMATION_SCHEMA.SCHEMATA')
                ->where([
                    'SCHEMA_NAME' => 'other'
                ])
                ->execute()
                ->all()
        );
    }

    public function testDropSchemaSql(): void
    {
        $this->assertSame(
            'DROP SCHEMA test',
            $this->forge->dropSchemaSql('test')
        );
    }

    public function testDropSchemaSqlIfExists(): void
    {
        $this->assertSame(
            'DROP SCHEMA IF EXISTS test',
            $this->forge->dropSchemaSql('test', ['ifExists' => true])
        );
    }

}
