<?php

namespace Persistence\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Persistence\Model\Amount;

/**
 * Valida importes
 *
 * @author Rubén Sarrió <rubensarrio@gmail.com>
 */
class ValidAmountValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof Amount) {
            if ($value->getValue() === null) {
                $this->context->addViolationAt('value', $constraint->message);
            }
        } else {
            $this->context->addViolationAt('value', $constraint->message);
        }
    }
}
