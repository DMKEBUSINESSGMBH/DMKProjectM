<?php

namespace DMKProjectM\Bundle\ProjectBundle\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectSelectType extends AbstractType
{
    /** @var Registry */
    private $doctrine;

    /**
     * @param Registry               $doctrine
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
                'autocomplete_alias' => 'projectm_projects', // für oro searchhandler
                'create_form_route'  => 'projectm_project_project_create',
                'configs'            => [
                    'placeholder' => 'dmkprojectm.project.form.choose_task',
                ],
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
        return 'projectm_project_select';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'oro_entity_create_or_select_inline';
    }
}
