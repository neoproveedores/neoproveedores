<?php

namespace AppBundle\Form;

use Persistence\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para los datos de usuario
 */
class UserType extends AbstractType
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
            ->add('role', 'choice', [
                'choices' => [
                    'ROLE_MANAGER' => 'Gestor',
                    'ROLE_PROJECT_MANAGER' => 'Gestor de proyectos',
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
