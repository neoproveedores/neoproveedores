<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para el presupuesto de un proyecto
 */
class BudgetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', new AmountType())
            ->add('timing', new TimingType())
            ->add('notes', 'textarea', [
                'attr' => [
                    'maxlength' => 500,
                    'placeholder' => 'Notas sobre el presupuesto',
                ],
            ])
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
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Budget']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'budget';
    }
}
