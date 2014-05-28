<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:52
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface DrivingAssertionRepository extends CommonBaseRepository{

    /**
     * @param DrivingAssertion $drivingAssertion
     * @return mixed
     */
    public function store(DrivingAssertion $drivingAssertion);

    /**
     * @param DrivingAssertion $drivingAssertion
     * @return mixed
     */
    public function remove(DrivingAssertion $drivingAssertion);

    public function findAllActiveByShift(Shift $shift);
} 