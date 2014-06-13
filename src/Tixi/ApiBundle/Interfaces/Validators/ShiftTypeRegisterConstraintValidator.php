<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 23.04.14
 * Time: 16:34
 */

namespace Tixi\ApiBundle\Interfaces\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Tixi\ApiBundle\Interfaces\Management\ShiftTypeRegisterDTO;

/**
 * Class DateRangeConstraintValidator
 * @package Tixi\ApiBundle\Shared\Validator
 */
class ShiftTypeRegisterConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param ShiftTypeRegisterDTO $dto
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($dto, Constraint $constraint) {
        if ($dto->start !== null && $dto->end !== null) {
            if ($dto->start >= $dto->end) {
                $this->context->addViolationAt(
                    'start',
                    'valid.start_before_end_shifttype',
                    array(), null
                );
            }
        }
    }
}