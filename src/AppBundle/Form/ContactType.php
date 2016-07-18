<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para un contacto
 */
class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('avatar', new AvatarType())
            ->add('firstName', null, [
                'attr' => ['placeholder' => 'Nombre'],
            ])
            ->add('lastName', null, [
                'attr' => ['placeholder' => 'Apellidos'],
            ])
            ->add('businessName', null, [
                'attr' => ['placeholder' => 'Razón social'],
            ])
            ->add('position', null, [
                'attr' => ['placeholder' => 'Puesto en la empresa'],
            ])
            ->add('email', 'email', [
                'attr' => ['placeholder' => 'Correo electrónico'],
            ])
            ->add('phone', null, [
                'attr' => ['placeholder' => 'Teléfono'],
            ])
            ->add('alternatePhone', null, [
                'attr' => ['placeholder' => 'Otro teléfono'],
            ])
            ->add('web', null, [
                'attr' => ['placeholder' => 'Página web'],
            ])
            ->add('facebook', null, [
                'attr' => ['placeholder' => 'Facebook'],
            ])
            ->add('twitter', null, [
                'attr' => ['placeholder' => 'Twitter'],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Contact']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'contact';
    }
}
