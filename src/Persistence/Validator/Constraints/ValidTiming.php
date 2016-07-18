<?php

namespace Persistence\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidTiming extends Constraint
{
    public $message = 'Intervalo temporal inválido';
    public $rearMessage = 'La fecha inicial no puede ser posterior a la final.';
    public $prevMessage = 'La fecha final no puede ser anterior a la inicial.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
