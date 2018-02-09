<?php
namespace DMKProjectM\Bundle\ProjectBundle\Form\Type;

use DMKProjectM\Bundle\ProjectBundle\Entity\Project;
use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    const LABEL_PREFIX = 'dmkprojectm.project.';

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'projectm_project';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => self::LABEL_PREFIX.'name.label'
                ]
            )
            ->add(
                'description',
                OroRichTextType::class,
                [
                    'label' => self::LABEL_PREFIX.'description.label',
                ]
            )
            ->add(
                'isActive',
                CheckboxType::class,
                [
                    'label' => self::LABEL_PREFIX.'is_active.label',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Project::class,
            ]
        );
    }
}
