<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Primer formulario para los contactos de un proveedor
 */
class ProviderContactsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('freelanceContact', 'checkbox', [
                'required' => false,
            ])
            ->add('contacts', 'collection', [
                'type' => new ContactType(),
                'allow_add' => true,
                'allow_delete' => true,
            ])
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
        return 'provider_contacts';
    }
}
