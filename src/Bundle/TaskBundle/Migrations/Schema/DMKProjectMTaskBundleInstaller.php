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
use DMKProjectM\Bundle\TaskBundle\Entity\Task;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;

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
        self::addTaskStatusField($schema, $this->extendExtension);
        self::addTaskTypeField($schema, $this->extendExtension);
    }

    public static function addTaskStatusField(Schema $schema, ExtendExtension $extendExtension)
    {
        $enumTable = $extendExtension->addEnumField(
            $schema,
            'projectm_task',
            'status',
            Task::INTERNAL_ENUM_CODE_STATUS,
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
                'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'dataaudit' => ['auditable' => true],
                'importexport' => ["order" => 120, "short" => true]
            ]
        );
        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', [
            Task::STATUS_OPEN,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_CLOSED
        ]);

        $enumTable->addOption(OroOptions::KEY, $options);
    }
    public static function addTaskTypeField(Schema $schema, ExtendExtension $extendExtension)
    {
        $enumTable = $extendExtension->addEnumField(
            $schema,
            'projectm_task',
            'tasktype',
            Task::INTERNAL_ENUM_CODE_TYPE,
            false,
            false,
            [
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
                'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'dataaudit' => ['auditable' => true],
                'importexport' => ["order" => 130, "short" => true]
            ]
            );
        $options = new OroOptions();
        $options->set('enum', 'immutable_codes', [
            Task::TYPE_MILESTONE,
            Task::TYPE_TASK,
        ]);

        $enumTable->addOption(OroOptions::KEY, $options);

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
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('project', 'integer', ['notnull' => false]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['task_priority_name'], 'idx_55bc0406d34c1e8e', []);
        $table->addIndex(['owner_id'], 'idx_55bc04067e3c61f9', []);
        $table->addIndex(['organization_id'], 'idx_55bc040632c8a3de', []);
        $table->addIndex(['due_date'], 'task_due_date_idx');
        $table->addIndex(['start_date'], 'pmtask_start_date_idx', []);

        $table->addIndex(['parent_id'], 'idx_55BC0406727ACA70', []);
        $table->addIndex(['project'], 'idx_55BC04062FB3D0EE', []);

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
            $schema->getTable('projectm_project'),
            ['project'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
            );
        $table->addForeignKeyConstraint(
            $schema->getTable('projectm_task'),
            ['parent_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
            );
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
    }
}
