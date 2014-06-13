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
use Tixi\ApiBundle\Interfaces\PersonRegisterDTO;

/**
 * Class DateRangeConstraintValidator
 * @package Tixi\ApiBundle\Shared\Validator
 */
class PersonRegisterConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param PersonRegisterDTO $dto
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($dto, Constraint $constraint) {
        if ($dto->birthday !== null) {
            if ($dto->birthday > new \DateTime('today')) {
                $this->context->addViolationAt(
                    'birthday',
                    'valid.birthday_in_future',
                    array(), null
                );
            }
        }
    }
}