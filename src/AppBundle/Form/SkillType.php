<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Loader\AbilitiesLoader;

/**
 * Formulario para una habilidad
 */
class SkillType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct($dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ability', null, [
                'empty_data' => null,
                'placeholder' => 'Escribe para seleccionar',
                'choice_loader' => new AbilitiesLoader($this->dm),
                'attr' => [
                    'class' => 'ui search add dropdown',
                    'placeholder' => 'Escribe para seleccionar',
                ],
            ])
            ->add('rating', 'hidden')
            ->add('hourRate', new AmountType())
            ->add('notes', 'textarea', [
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => 'Notas sobre la habilidad',
                ],
            ])
            ->add('files', 'collection', [
                'type' => new FileType(),
                'allow_add' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Skill']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'skill';
    }
}
