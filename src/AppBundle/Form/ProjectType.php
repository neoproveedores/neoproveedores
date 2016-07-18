<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Loader\AbilitiesLoader;

/**
 * Formulario para un proyecto
 */
class ProjectType extends AbstractType
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
        $description = 'Escribe una breve descripción del proyecto';
        $briefing = 'Escribe el brief del proyecto lo más completo posible';

        $builder
            ->add('name', null, [
                'attr' => ['placeholder' => 'Escribe el título del proyecto'],
            ])
            ->add('code', null, [
                'attr' => ['placeholder' => 'Escribe el código'],
            ])
            ->add('description', 'textarea', [
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => $description,
                ],
            ])
            ->add('abilities', null, [
                'expanded' => false,
                'multiple' => true,
                'choice_loader' => new AbilitiesLoader($this->dm),
                'attr' => [
                    'class' => 'ui search add dropdown',
                    'placeholder' => 'Escribe para seleccionar habilidades',
                ],
            ])
            ->add('timing', new TimingType())
            ->add('briefing', 'text', [
                'attr' => [
                    'placeholder' => $briefing,
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
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Project']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project';
    }
}
