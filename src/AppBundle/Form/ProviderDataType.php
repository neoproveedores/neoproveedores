<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Persistence\Model\Provider;

/**
 * Formulario para los datos de un proveedor
 */
class ProviderDataType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', [
                'choices' => [
                    Provider::ACTIVE => 'Disponible',
                    Provider::UNAVAILABLE => 'No disponible',
                ],
                'attr' => [
                    'class' => 'ui dropdown',
                ],
            ])
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
            ->add('contact', new ContactType())
            ->add('billing', new BillingIdentType())
            ->add('address', new AddressType())
            ->add('hourRate', new AmountType())
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
        return 'provider_data';
    }
}
