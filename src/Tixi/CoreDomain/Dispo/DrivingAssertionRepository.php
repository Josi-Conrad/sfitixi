<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 21:52
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface DrivingAssertionRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface DrivingAssertionRepository extends CommonBaseRepository {

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

    /**
     * @param Shift $shift
     * @return mixed
     */
    public function findAllActiveByShift(Shift $shift);

    /**
     * @param Driver $driver
     * @return mixed
     */
    public function findAllProspectiveByDriver(Driver $driver);

    /**
     * @param RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan
     * @return mixed
     */
    public function findAllProspectiveByRepeatedDrivingAssertionPlan(RepeatedDrivingAssertionPlan $repeatedDrivingAssertionPlan);

} 