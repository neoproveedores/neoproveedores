<?php

namespace Persistence\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidIdentificationDocument extends Constraint
{
    public $message = 'Documento de identificación inválido';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return
            'Persistence\Validator\Constraints\IdentificationDocumentValidator'
        ;
    }
}
