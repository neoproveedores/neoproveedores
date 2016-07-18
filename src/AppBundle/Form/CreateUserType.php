<?php

namespace AppBundle\Form;

use Persistence\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para la creación de un usuario
 */
class CreateUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contact', new ContactType())
            ->add('email', null, [
                'attr' => ['placeholder' => 'Email'],
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'first_options' => [
                    'attr' => ['placeholder' => 'Contraseña'],
                ],
                'second_options' => [
                    'attr' => ['placeholder' => 'Repetir contraseña'],
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
            ])
            ->add('role', 'choice', [
                'choices' => [
                    'ROLE_MANAGER' => 'Gestor',
                    'ROLE_PROVIDER' => 'Proveedor',
                ],
                'attr' => [
                    'class' => 'ui dropdown',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\User']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
