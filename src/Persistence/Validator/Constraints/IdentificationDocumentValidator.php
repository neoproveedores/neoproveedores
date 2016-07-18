<?php

namespace Persistence\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use IsoCodes\Nif;
use IsoCodes\Cif;

/**
 * Valida documentos de identificación
 */
class IdentificationDocumentValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (! Nif::validate($value) && ! Cif::validate($value)) {
            $this->context->addViolationAt('value', $constraint->message);
        }
    }
}
