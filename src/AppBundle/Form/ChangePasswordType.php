<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para cambiar la contraseña
 */
class ChangePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password');
        $builder->add('newPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Los passwords deben coincidir',
            'required' => true,
            'first_options'  => array('label' => ' '),
            'second_options' => array('label' => ' '),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Persistence\Model\ChangePassword',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'change_passwd';
    }
}
