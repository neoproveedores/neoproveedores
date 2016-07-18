<?php

namespace Persistence\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;
use Persistence\Model\Timing;

/**
 * Valida intervalos temporales
 */
class ValidTimingValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $notBlank = (new NotBlank())->message;

        if (is_null($value)) {
            $this->addDoubleViolation($notBlank, $notBlank);
        }

        if ($value instanceof Timing) {
            $start = $value->getStart();
            $end = $value->getEnd();
            $notDate = (new Date())->message;

            if (! $start instanceof \DateTime) {
                $this->context->addViolationAt('start', $notDate);
            }

            if (! $end instanceof \DateTime) {
                $this->context->addViolationAt('end', $notDate);
            }

            if ($start instanceof \DateTime and $end instanceof \DateTime) {
                if ($start > $end) {
                    $this->addDoubleViolation(
                        $constraint->rearMessage,
                        $constraint->prevMessage
                    );
                }
            }
        }
    }

    /**
     * @param string $startMessage
     * @param string $endMessage
     */
    public function addDoubleViolation($startMessage, $endMessage)
    {
        $this->context->addViolationAt('start', $startMessage);
        $this->context->addViolationAt('end', $endMessage);
    }
}
