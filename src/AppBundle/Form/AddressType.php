<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para una dirección
 */
class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street', null, [
                'attr' => ['placeholder' => 'Calle'],
            ])
            ->add('postalCode', null, [
                'attr' => ['placeholder' => 'Código postal'],
            ])
            ->add('city', null, [
                'attr' => ['placeholder' => 'Localidad'],
            ])
            ->add('region', null, [
                'attr' => ['placeholder' => 'Provincia'],
            ])
            ->add('country', null, [
                'attr' => ['placeholder' => 'País'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Address']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'address';
    }
}
