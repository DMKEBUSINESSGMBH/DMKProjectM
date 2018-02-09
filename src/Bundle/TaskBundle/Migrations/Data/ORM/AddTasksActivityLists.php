<?php

namespace DMKProjectM\Bundle\TaskBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\ActivityListBundle\Migrations\Data\ORM\AddActivityListsData;

class AddTasksActivityLists extends AddActivityListsData implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return ['DMKProjectM\Bundle\TaskBundle\Migrations\Data\ORM\UpdateTaskWithOrganization'];
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->addActivityListsForActivityClass(
            $manager,
            'DMKProjectMTaskBundle:Task',
            'owner',
            'organization'
        );
    }
}
