<?php
namespace DMKProjectM\Bundle\ProjectBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use EHDev\BasicsBundle\Migrations\BaseEntityMigrationTrait;
use EHDev\BasicsBundle\Migrations\BUOwnerMigrationTrait;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class InitialProjectTable implements Migration
{
    use BaseEntityMigrationTrait;
    use BUOwnerMigrationTrait;

    public function up(Schema $schema, QueryBag $queries)
    {
        self::createProjecttable($schema);
    }

    public static function createProjecttable(Schema $schema)
    {
        $table = $schema->createTable('projectm_project');
        self::migrateBaseEntity($table);
        self::migrationBUOwner($table, $schema);

        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('is_active', 'boolean');
    }
}
