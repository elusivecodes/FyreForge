<?php
declare(strict_types=1);

namespace Tests\Postgres\Forge;

trait CreateSchemaTestTrait
{
    public function testCreateSchema(): void
    {
        $this->forge->createSchema('other', [
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);

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
}
