<?php

namespace DMKProjectM\Bundle\TaskBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\WorkflowBundle\Helper\WorkflowQueryTrait;
use DMKProjectM\Bundle\TaskBundle\Entity\Task;

class TaskRepository extends EntityRepository
{
    use WorkflowQueryTrait;
    const CLOSED_STATE = 'closed';

    /**
     * @param int $userId
     * @param int $limit
     *
     * @return Task[]
     */
    public function getTasksAssignedTo($userId, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('task');
        $this->joinWorkflowStep($queryBuilder, 'workflowStep');

        return $queryBuilder
            ->where('task.owner = :assignedTo AND workflowStep.name != :step')
            ->orderBy('task.dueDate', 'ASC')
            ->addOrderBy('workflowStep.id', 'ASC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->setParameter('assignedTo', $userId)
            ->setParameter('step', TaskRepository::CLOSED_STATE)
            ->getQuery()
            ->execute();
    }

    /**
     * Returns a query builder which can be used to get a list of tasks filtered by start and end dates
     *
     * @param int $userId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string[] $extraFields
     *
     * @return QueryBuilder
     */
    public function getTaskListByTimeIntervalQueryBuilder($userId, $startDate, $endDate, $extraFields = [])
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id, t.subject, t.description, t.dueDate, t.createdAt, t.updatedAt')
            ->where('t.owner = :assignedTo AND t.dueDate >= :start AND t.dueDate <= :end')
            ->setParameter('assignedTo', $userId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);
        if ($extraFields) {
            foreach ($extraFields as $field) {
                $qb->addSelect('t.' . $field);
            }
        }

        return $qb;
    }
}
