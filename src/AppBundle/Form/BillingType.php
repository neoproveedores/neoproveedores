<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para los datos de facturación
 */
class BillingType extends AbstractType
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
                    'placeholder' => 'Notas sobre la facturación',
                ],
            ])
            ->add('bankAccount', null, [
                'attr' => ['placeholder' => 'Número de cuenta IBAN'],
            ])
            ->add('bankAccountCode', null, [
                'attr' => [
                    'placeholder' => 'Código SWIFT o BIC de la cuenta',
                ],
            ])
            ->add('taxIdentFile', new FileType())
            ->add('taxIdentAdditionalFile', new FileType())
            ->add('taxCertificateFile', new FileType())
            ->add('socialSecurityCertificateFile', new FileType())
            ->add('files', 'collection', [
                'type' => new FileType(),
                'allow_add' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Billing']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'billing';
    }
}
