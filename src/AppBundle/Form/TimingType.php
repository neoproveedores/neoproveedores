<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Formulario para un intervalo temporal
 */
class TimingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new CallbackTransformer(
            function ($originalDate) {
                if ($originalDate instanceof \DateTime) {
                    return $originalDate->format('Y-m-d');
                }
            },
            function ($submittedDate) {
                $date = \DateTime::createFromFormat('Y-m-d', $submittedDate);

                if ($date instanceof \DateTime) {
                    return $date;
                }
            }
        );

        $builder
            ->add('start', 'text', [
                'attr' => ['placeholder' => 'Selecciona la fecha'],
            ])
            ->add('end', 'text', [
                'attr' => ['placeholder' => 'Selecciona la fecha'],
            ])
        ;

        $builder->get('start')->addModelTransformer($transformer);
        $builder->get('end')->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Persistence\Model\Timing']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'timing';
    }
}
