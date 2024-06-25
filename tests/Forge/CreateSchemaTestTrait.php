<?php
declare(strict_types=1);

namespace Tests\Forge;

trait CreateSchemaTestTrait
{
    public function testCreateSchema(): void
    {
        $this->forge->createSchema('other');

        $this->assertCount(
            1,
            $this->db->select()
                ->from('INFORMATION_SCHEMA.SCHEMATA')
                ->where([
                    'SCHEMA_NAME' => 'other',
                ])
                ->execute()
                ->all()
        );
    }

    public function testCreateSchemaSql(): void
    {
        $this->assertSame(
            'CREATE SCHEMA test CHARACTER SET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            $this->forge->createSchemaSql('test')
        );
    }

    public function testCreateSchemaSqlCharsetCollation(): void
    {
        $this->assertSame(
            'CREATE SCHEMA test CHARACTER SET = \'utf8\' COLLATE = \'utf8_unicode_ci\'',
            $this->forge->createSchemaSql('test', [
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
            ])
        );
    }

    public function testCreateSchemaSqlIfNotExists(): void
    {
        $this->assertSame(
            'CREATE SCHEMA IF NOT EXISTS test CHARACTER SET = \'utf8mb4\' COLLATE = \'utf8mb4_unicode_ci\'',
            $this->forge->createSchemaSql('test', [
                'ifNotExists' => true,
            ])
        );
    }
}
