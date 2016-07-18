<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Persistence\Model\Notifications;
use Persistence\Model\Event;

/**
 * Formulario para la configuración de las notificaciones
 */
class NotificationsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', 'checkbox')
            ->add('enabledEvents', 'choice', [
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    Event::INVITE => 'Cada vez que me inviten a un proyecto',
                    Event::MESSAGE => 'Cada vez que se envía un mensaje',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Persistence\Model\Notifications',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'notifications';
    }
}
