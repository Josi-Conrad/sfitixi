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
use Tixi\ApiBundle\Interfaces\ServicePlanRegisterDTO;

/**
 * Class DateRangeConstraintValidator
 * @package Tixi\ApiBundle\Shared\Validator
 */
class ServicePlanRegisterConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param ServicePlanRegisterDTO $dto
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($dto, Constraint $constraint) {
        if ($dto->start !== null && $dto->end !== null) {
            if ($dto->start > $dto->end) {
                $this->context->addViolationAt(
                    'memo',
                    'valid.start_before_end',
                    array(), null
                );
            }
        }
    }
}