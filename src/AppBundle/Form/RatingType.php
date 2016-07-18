<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para valorar el trabajo de un proveedor en un proyecto
 */
class RatingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('competences', 'collection', [
                'type' => new CompetenceRatingType(),
            ])
            ->add('notes', 'textarea', [
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => 'Escribe un comentario adicional',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Rating']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rating';
    }
}
