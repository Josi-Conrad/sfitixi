<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 08.04.14
 * Time: 21:08
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface RepeatedDrivingAssertionRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface RepeatedDrivingAssertionRepository extends CommonBaseRepository {

    /**
     * @param RepeatedDrivingAssertion $assertion
     * @return mixed
     */
    public function store(RepeatedDrivingAssertion $assertion);

    /**
     * @param RepeatedDrivingAssertion $assertion
     * @return mixed
     */
    public function remove(RepeatedDrivingAssertion $assertion);

    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function getAmountByShiftType(ShiftType $shiftType);
} 