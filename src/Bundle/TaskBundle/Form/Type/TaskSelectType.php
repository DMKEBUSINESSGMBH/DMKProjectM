<?php

namespace DMKProjectM\Bundle\TaskBundle\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

class TaskSelectType extends AbstractType
{
    /** @var Registry */
    private $doctrine;

    /**
     * @param Registry               $doctrine
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'placeholder' => 'dmkprojectm.task.form.choose_task',
                'empty_data'  => null,
                'class'       => 'DMKProjectM\Bundle\TaskBundle\Entity\Task',
            ]
        );

//         $queryBuilderNormalizer = function () {
//             $qb = $this->doctrine->getRepository('OroOrganizationBundle:BusinessUnit')
//                 ->createQueryBuilder('bu');

//             $qb->select('bu')
//                 ->where('bu.organization = :organization');

//             $qb->setParameter('organization', $this->tokenAccessor->getOrganization());

//             return $qb;
//         };

//         $resolver->setNormalizer('query_builder', $queryBuilderNormalizer);
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
        return 'projectm_task_select';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'genemu_jqueryselect2_entity';
    }
}
