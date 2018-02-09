<?php
namespace DMKProjectM\Bundle\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;
use DMKProjectM\Bundle\TaskBundle\Entity\Task;

class LoadTaskTypeEnum extends AbstractEnumFixture
{
    protected function getEnumCode()
    {
        return Task::INTERNAL_ENUM_CODE_TYPE;
    }

    protected function getData()
    {
        return [
            Task::TYPE_TASK => 'Task',
            Task::TYPE_MILESTONE => 'Milestone',
        ];

    }

}
