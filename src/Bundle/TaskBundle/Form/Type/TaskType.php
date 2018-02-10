<?php

namespace DMKProjectM\Bundle\TaskBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Oro\Bundle\FormBundle\Utils\FormUtils;
use DMKProjectM\Bundle\TaskBundle\Entity\Task;

class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subject',
                'text',
                [
                    'required' => true,
                    'label' => 'dmkprojectm.task.subject.label'
                ]
            )
            ->add(
                'description',
                'oro_resizeable_rich_text',
                [
                    'required' => false,
                    'label' => 'dmkprojectm.task.description.label'
                ]
            );
        $this->addDueDateField($builder);
        $this->addStartDateField($builder);
        $builder
            ->add(
                'status',
                'oro_enum_select',
                [
                    'label' => 'dmkprojectm.task.status.label',
                    'enum_code' => Task::INTERNAL_ENUM_CODE_STATUS,
                    'required' => true,
                    'constraints' => [new Assert\NotNull()]
                ]
            )

            ->add(
                'project',
                'projectm_project_select',
                [
                    'label' => 'dmkprojectm.task.project.label',
                    'required' => false,
                ]
            )
            ->add(
                'parent',
                'projectm_task_select',
                [
                    'label' => 'dmkprojectm.task.parent.label',
                    'required' => false,
                ]
            )
            ->add(
                'taskPriority',
                'translatable_entity',
                [
                    'label' => 'dmkprojectm.task.task_priority.label',
                    'class' => 'DMKProjectM\Bundle\TaskBundle\Entity\TaskPriority',
                    'required' => true,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('priority')->orderBy('priority.order');
                    }
                ]
            )
            ->add(
                'reminders',
                'oro_reminder_collection',
                [
                    'required' => false,
                    'label' => 'oro.reminder.entity_plural_label'
                ]
            );
        $builder->addEventListener(FormEvents::POST_SET_DATA, [$this, 'postSetData']);
    }

    /**
     * Post set data handler
     *
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $this->updateDueDateFieldConstraints($event);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Task::class,
                'intention' => 'task',
                'cascade_validation' => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'projectm_task';
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addStartDateField(FormBuilderInterface $builder)
    {
        $builder
        ->add(
            'startDate',
            'oro_datetime',
            [
                'required' => false,
                'label' => 'dmkprojectm.task.start_date.label',
            ]
            );
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addDueDateField(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'dueDate',
                'oro_datetime',
                [
                    'required' => false,
                    'label' => 'dmkprojectm.task.due_date.label',
                    'constraints' => [
                        $this->getDueDateValidationConstraint(new \DateTime('now', new \DateTimeZone('UTC')))
                    ]
                ]
            );
    }

    /**
     * @param FormEvent $event
     */
    protected function updateDueDateFieldConstraints(FormEvent $event)
    {
        /** @var Task $data */
        $data = $event->getData();
        if ($data && $data->getCreatedAt()) {
            FormUtils::replaceField(
                $event->getForm(),
                'dueDate',
                [
                    'constraints' => [
                        $this->getDueDateValidationConstraint($data->getCreatedAt())
                    ]
                ]
            );
        }
    }

    /**
     * @param \DateTime $startDate
     *
     * @return Assert\GreaterThanOrEqual
     */
    protected function getDueDateValidationConstraint(\DateTime $startDate)
    {
        return new Assert\GreaterThanOrEqual(
            [
                'value'   => $startDate,
                'message' => 'Due date must not be in the past'
            ]
        );
    }
}
