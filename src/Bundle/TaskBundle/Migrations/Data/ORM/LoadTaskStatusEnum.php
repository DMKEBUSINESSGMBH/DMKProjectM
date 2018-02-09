<?php
namespace DMKProjectM\TaskBundle\Migrations\Data\ORM;

use Oro\Bundle\EntityExtendBundle\Migration\Fixture\AbstractEnumFixture;
use DMKProjectM\Bundle\TaskBundle\Entity\Task;

class LoadTaskStatusEnum extends AbstractEnumFixture
{
    protected function getEnumCode()
    {
        return Task::STATUS_OPEN;
    }

    protected function getData()
    {
        return [
            Task::STATUS_OPEN => 'Open',
            Task::STATUS_IN_PROGRESS => 'In Progress',
            Task::STATUS_CLOSED => 'Closed',
        ];
    }

}