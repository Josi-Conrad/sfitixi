<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 17.05.14
 * Time: 20:29
 */

namespace Tixi\App\AppBundle\Disposition;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Disposition\WorkingMonthManagement;
use Tixi\CoreDomain\BankHolidayRepository;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\WorkingMonth;

class WorkingMonthManagementImpl extends ContainerAware implements WorkingMonthManagement {

    /**
     * Assign all available and possible drivers to a drivingPool for corresponding workingMonth
     * @param \Tixi\CoreDomain\Dispo\WorkingMonth $workingMonth
     * @return mixed
     */
    public function assignAvailableDriversToDrivingPools(WorkingMonth $workingMonth) {
        $em = $this->container->get('entity_manager');
        $driverRepo = $this->container->get('driver_repository');
        $bankHolidayRepo = $this->container->get('bankholiday_repository');

        $drivers = $driverRepo->findAllActive();

        //random shuffle drivers array for better usage of all people
        shuffle($drivers);

        //TODO: tag Drivers with multiple shifts
        $workingDays = $workingMonth->getWorkingDays();
        foreach ($workingDays as $workingDay) {
            $isBankHoliday = $bankHolidayRepo->checkIfWorkingDayIsBankHoliday($workingDay);
            $shifts = $workingDay->getShifts();
            foreach ($drivers as $driver) {
                foreach ($shifts as $shift) {
                    //driver works already in this shift
                    if ($shift->isDriverAssociatedToThisShift($driver)) {
                        continue;
                    }
                    $drivingPools = $shift->getDrivingPoolsWithoutDriver();
                    foreach ($drivingPools as $pool) {
                        if ($driver->isAvailableOn($shift, $isBankHoliday)) {
                            $pool->assignDriver($driver);
                            $pool->setStatus(DrivingPool::WAITING_FOR_CONFIRMATION);
                            break;
                        }
                    }
                }
            }
        }
        $em->flush();
    }

    /**
     * @param WorkingMonth $workingMonth
     * @return DrivingPool[]
     */
    public function getAllUnassignedDrivingPoolsForMonth(WorkingMonth $workingMonth) {
        $unassignedDrivingPools = array();
        $workingDays = $workingMonth->getWorkingDays();
        foreach ($workingDays as $workingDay) {
            $shifts = $workingDay->getShifts();
            foreach ($shifts as $shift) {
                $drivingPools = $shift->getDrivingPools();
                /**@var $drivingPool DrivingPool */
                foreach ($drivingPools as $drivingPool) {
                    if (!$drivingPool->hasAssociatedDriver()) {
                        array_push($unassignedDrivingPools, $drivingPool);
                    }
                }
            }
        }
        return $unassignedDrivingPools;
    }
}