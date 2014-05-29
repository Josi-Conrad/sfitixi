<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 17:59
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface RepeatedDrivingAssertionPlanRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface RepeatedDrivingAssertionPlanRepository extends CommonBaseRepository {
    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed
     */
    public function store(RepeatedDrivingAssertionPlan $assertionPlan);

    /**
     * @param RepeatedDrivingAssertionPlan $assertionPlan
     * @return mixed
     */
    public function remove(RepeatedDrivingAssertionPlan $assertionPlan);

    /**
     * Gives all active repeatedDrivingAssertionPlans for given date/day
     * @param \DateTime $date
     * @return array
     */
    public function findPlanForDate(\DateTime $date);

    public function findActivePlansInRangeOfWorkingMonth(WorkingMonth $workingMonth);

    public function findAllProspectiveForDriver(Driver $driver);

}