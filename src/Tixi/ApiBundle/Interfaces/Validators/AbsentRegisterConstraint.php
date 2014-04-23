<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 23.04.14
 * Time: 16:55
 */

namespace Tixi\ApiBundle\Interfaces\Validators;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AbsentRegisterConstraint extends Constraint {
    /**
     * @return array|string
     */
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
}