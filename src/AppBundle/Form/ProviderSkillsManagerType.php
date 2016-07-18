<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para las habilidades de un proveedor
 */
class ProviderSkillsManagerType extends ProviderSkillsType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skillsClosed', 'checkbox')
            ->add('skills', 'collection', [
                'type' => new SkillManagerType($this->dm),
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'provider_skills_manager';
    }
}
