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

/**
 * Class DateRangeConstraintValidator
 * @package Tixi\ApiBundle\Shared\Validator
 */
class AbsentRegisterConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $dto
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate($dto, Constraint $constraint) {
        if ($dto->startDate !== null && $dto->endDate !== null) {
            if ($dto->startDate > $dto->endDate) {
                $this->context->addViolationAt(
                    'startDate',
                    'valid.startdate_before_enddate',
                    array(), null
                );
            }
        }
    }
}