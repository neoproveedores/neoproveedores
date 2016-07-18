<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Persistence\Model\Provider;

/**
 * Formulario para los registrar un proveedor
 */
class ProviderRegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('kind', 'choice', [
                'choices' => [
                    Provider::FREELANCE => 'Autónomo',
                    Provider::COMPANY => 'Empresa',
                ],
                'attr' => [
                    'class' => 'ui dropdown',
                    'data-trigger' => 'toggle',
                ],
            ])
            ->add('contact', new ContactRegistrationType())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Persistence\Model\Provider',
            'validation_groups' => ['provider_registration'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'provider_registration';
    }
}
