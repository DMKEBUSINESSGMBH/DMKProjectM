<?php
namespace DMKProjectM\Bundle\ProjectBundle\Migrations\Schema;

use DMKProjectM\Bundle\ProjectBundle\Migrations\Schema\v1_0\InitialProjectTable;
use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class DMKProjectMProjectBundleInstaller implements Installation
{
    public function getMigrationVersion()
    {
        return 'v1_0';
    }
    
    public function up(Schema $schema, QueryBag $queries)
    {
        /** v1_0 */
        InitialProjectTable::createProjecttable($schema);
    }
}
