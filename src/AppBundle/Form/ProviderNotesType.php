<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para las notas de un proveedor
 */
class ProviderNotesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notes', 'textarea', [
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => 'Notas sobre el proveedor',
                ],
            ])
            ->add('timing', new TimingType())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Persistence\Model\Provider',
            'validation_groups' => [$this->getName()],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'provider_notes';
    }
}
