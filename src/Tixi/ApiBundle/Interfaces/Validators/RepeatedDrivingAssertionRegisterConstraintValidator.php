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
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionRegisterDTO;

/**
 * Class DateRangeConstraintValidator
 * @package Tixi\ApiBundle\Shared\Validator
 */
class RepeatedDrivingAssertionRegisterConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param RepeatedDrivingAssertionRegisterDTO $dto
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($dto, Constraint $constraint) {
        if ($dto->anchorDate !== null && $dto->endDate !== null) {
            if ($dto->anchorDate > $dto->endDate) {
                $this->context->addViolationAt(
                    'anchorDate',
                    'valid.anchordate_before_enddate',
                    array(), null
                );
            }
            if (7 > (date_diff($dto->endDate, $dto->anchorDate)->days)) {
                $this->context->addViolationAt(
                    'endDate',
                    'valid.enddate_weekly',
                    array(), null
                );
            }
        }
    }
}