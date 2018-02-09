<?php

namespace DMKProjectM\Bundle\TaskBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtension;
use Oro\Bundle\CommentBundle\Migration\Extension\CommentExtensionAwareInterface;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use OroCRM\Bundle\TaskBundle\Migrations\Schema\v1_9\AddActivityAssociations;
use OroCRM\Bundle\TaskBundle\Migrations\Schema\v1_11_1\AddTaskStatusField;

class DMKProjectMTaskBundleInstaller implements
    Installation,
    ActivityExtensionAwareInterface,
    CommentExtensionAwareInterface,
    ExtendExtensionAwareInterface
{
    /** @var ActivityExtension */
    protected $activityExtension;

    /** @var CommentExtension */
    protected $comment;

    /** @var ExtendExtension */
    protected $extendExtension;

    /**
     * @param ActivityExtension $activityExtension
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }

    /**
     * @param CommentExtension $commentExtension
     */
    public function setCommentExtension(CommentExtension $commentExtension)
    {
        $this->comment = $commentExtension;
    }

    /**
     * @param ExtendExtension $extendExtension
     */
    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createTaskTable($schema);
        $this->createTaskPriorityTable($schema);

        /** Foreign keys generation **/
        $this->addProjectmTaskForeignKeys($schema);

        /** Add comment relation */
        $this->comment->addCommentAssociation($schema, 'projectm_task');

        self::addActivityAssociations($schema, $this->activityExtension);
        AddTaskStatusField::addTaskStatusField($schema, $this->extendExtension);
        AddTaskStatusField::addEnumValues($queries, $this->extendExtension);
    }

    /**
     * Enable activities
     *
     * @param Schema            $schema
     * @param ActivityExtension $activityExtension
     */
    public static function addActivityAssociations(Schema $schema, ActivityExtension $activityExtension)
    {
        $activityExtension->addActivityAssociation($schema, 'projectm_task', 'oro_email');
        $activityExtension->addActivityAssociation($schema, 'oro_email', 'projectm_task');
    }

    /**
     * @param Schema $schema
     */
    protected function createTaskTable(Schema $schema)
    {
        $table = $schema->createTable('projectm_task');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('subject', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('description', 'text', ['notnull' => false]);
        $table->addColumn('due_date', 'datetime', ['notnull' => false]);
        $table->addColumn('start_date', 'datetime', ['notnull' => false, 'comment' => '(DC2Type:datetime)']);
        $table->addColumn('task_priority_name', 'string', ['notnull' => false, 'length' => 32]);
        $table->addColumn('owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_item_id', 'integer', ['notnull' => false]);
        $table->addColumn('workflow_step_id', 'integer', ['notnull' => false]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['task_priority_name'], 'idx_55bc0406d34c1e8e', []);
        $table->addIndex(['owner_id'], 'idx_55bc04067e3c61f9', []);
        $table->addIndex(['organization_id'], 'idx_55bc040632c8a3de', []);
        $table->addIndex(['due_date'], 'task_due_date_idx');
        $table->addIndex(['start_date'], 'pmtask_start_date_idx', []);

        $table->addUniqueIndex(['workflow_item_id'], 'uniq_55bc04061023c4ee');
        $table->addIndex(['workflow_step_id'], 'idx_55bc040671fe882c', []);
        $table->addIndex(['updatedAt'], 'task_updated_at_idx', []);
    }

    /**
     * @param Schema $schema
     */
    protected function createTaskPriorityTable(Schema $schema)
    {
        $table = $schema->createTable('projectm_task_priority');
        $table->addColumn('name', 'string', ['notnull' => true, 'length' => 32]);
        $table->addColumn('label', 'string', ['notnull' => true, 'length' => 255]);
        $table->addColumn('`order`', 'integer', ['notnull' => true]);
        $table->setPrimaryKey(['name']);
        $table->addUniqueIndex(['label'], 'uniq_a052a7ea750e8');
    }

    /**
     * @param Schema $schema
     */
    protected function addProjectmTaskForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('projectm_task');
        $table->addForeignKeyConstraint(
            $schema->getTable('projectm_task_priority'),
            ['task_priority_name'],
            ['name'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['owner_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_item'),
            ['workflow_item_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_workflow_step'),
            ['workflow_step_id'],
            ['id'],
            ['onDelete' => 'SET NULL']
        );
    }
}
