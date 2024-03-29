<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:06
 */

namespace Tixi\App\AppBundle\Disposition;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Console\Tests\Helper\FormatterHelperTest;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanDriversPerShiftDTO;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanDrivingAssertionDTO;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyLeastDistance;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyTimeWindow;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\App\Disposition\DispositionManagement;
use Tixi\App\Driving\DrivingAssertionManagement;
use Tixi\CoreDomain\Dispo\DrivingAssertion;
use Tixi\CoreDomain\Dispo\DrivingAssertionRepository;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\DrivingPoolRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomain\Dispo\ShiftTypeRepository;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\DriverRepository;

/**
 * Class DispositionManagementImpl
 * @package Tixi\App\AppBundle\Disposition
 */
class DispositionManagementImpl extends ContainerAware implements DispositionManagement {
    /**
     * @param \DateTime $dayTime
     * @return null|Shift
     */
    public function getResponsibleShiftForDayAndTime(\DateTime $dayTime) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $shiftRepo = $this->container->get('shift_repository');
        $shiftsForDay = $shiftRepo->findShiftsForDay($dayTime);

        $pickTime = $timeService->convertToLocalDateTime($dayTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
            $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());
            if (DateTimeService::matchTimeBetweenTwoDateTimes($pickTime, $startTime, $endTime)) {
                return $shift;
            }
        }
        return null;
    }

    /**
     * @param Shift $shift
     * @return array|\Tixi\CoreDomain\Dispo\DrivingMission[]
     */
    public function getDrivingMissionsInShift(Shift $shift) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $drivingMissionRepo = $this->container->get('drivingmission_repository');
        $matchingDrivingMissions = array();

        $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
        $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());

        $shiftMinutesStart = $timeService->getMinutesOfDay($startTime);
        $shiftMinutesEnd = $timeService->getMinutesOfDay($endTime);

        $drivingMissions = $drivingMissionRepo->findDrivingMissionsForDay($shift->getDate());
        foreach ($drivingMissions as $drivingMission) {
            /**@var $drivingMission \Tixi\CoreDomain\Dispo\DrivingMission */
            $startMinute = $drivingMission->getServiceMinuteOfDay();
            $endMinute = $startMinute + $drivingMission->getServiceDuration();

            /** start or end of the order laps into a shift time */
            if ($shiftMinutesEnd > $shiftMinutesStart) {
                if ($startMinute >= $shiftMinutesStart && $endMinute <= $shiftMinutesEnd ||
                    $startMinute >= $shiftMinutesStart && $startMinute <= $shiftMinutesEnd
                ) {
                    array_push($matchingDrivingMissions, $drivingMission);
                }
            } else {
                //if endminute is smaller then start, the time lays between midnight
                //so check if compare is not outside this time over midnight
                if (($startMinute >= $shiftMinutesStart && $startMinute >= $endMinute)
                    || ($startMinute <= $shiftMinutesStart && $startMinute <= $endMinute)
                ) {
                    array_push($matchingDrivingMissions, $drivingMission);
                }
            }
        }
        return $matchingDrivingMissions;
    }

    /**
     * @param \DateTime $day
     * @return array|\Tixi\CoreDomain\Vehicle[]
     */
    public function getAvailableVehiclesForDay(\DateTime $day) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $vehicleRepo = $this->container->get('vehicle_repository');
        $allVehicles = $vehicleRepo->findAllNotDeleted();
        $vehicles = array();
        foreach ($allVehicles as $vehicle) {
            $servicePlans = $vehicle->getActualServicePlans();
            if ($servicePlans === null) {
                array_push($vehicles, $vehicle);
            } else {
                $isInService = false;
                foreach ($servicePlans as $servicePlan) {
                    $spStart = $timeService->convertToLocalDateTime($servicePlan->getStart())->setTime(0, 0);
                    $spEnd = $timeService->convertToLocalDateTime($servicePlan->getEnd())->setTime(0, 0);
                    if (($spStart == $day || $spEnd == $day)
                    ) {
                        $isInService = true;
                    }
                }
                if (!$isInService) {
                    array_push($vehicles, $vehicle);
                }
            }
        }
        return $vehicles;
    }

    /**
     * @param Shift $shift
     * @param $oldAmount
     * @param $newAmount
     * @return mixed|void
     * @throws \LogicException
     */
    public function processChangeInAmountOfDriversPerShift(Shift $shift, $oldAmount, $newAmount) {
        /** @var DrivingPoolRepository $drivingPoolRepository */
        $drivingPoolRepository = $this->container->get('drivingpool_repository');
        $delta = $newAmount - $oldAmount;

        if ($delta > 0) {
            //new driving pool(s) need to be created. Just create the new driving pool(s).
            for ($i = 0; $i < $delta; $i++) {
                $drivingPoolRepository->store(DrivingPool::registerDrivingPool($shift));
            }

        } elseif ($delta < 0) {
            //we need to remove driving pool(s). This operation is only permitted if enough empty driving pool(s) could
            //be found (a driving pool is considered to be empty if it has no associated driving missions).
            $amountOfPoolsToRemove = abs($delta);
            $poolsToRemove = array();
            $drivingPools = $shift->getDrivingPoolsAsArray();
            /** @var DrivingPool $drivingPool */
            foreach ($drivingPools as $drivingPool) {
                if ($drivingPool->getAmountOfAssociatedDrivingMissions() === 0) {
                    $poolsToRemove[] = $drivingPool;
                    $amountOfPoolsToRemove--;
                    if ($amountOfPoolsToRemove === 0) {
                        //we have enough pools to remove
                        break;
                    }
                }
            }
            if ($amountOfPoolsToRemove === 0) {
                foreach ($poolsToRemove as $poolToRemove) {
                    $shift->removeDrivingPool($poolToRemove);
                    $drivingPoolRepository->remove($poolToRemove);
                }
            } else {
                throw new \LogicException('not enough empty driving pools found to remove');
            }
        }
        $shift->setAmountOfDrivers($newAmount);
    }

    /**
     * @param $year
     * @param $month
     * @return array|null|WorkingMonth
     */
    public function openWorkingMonth($year, $month) {
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->container->get('workingmonth_repository');
        /** @var WorkingDayRepository $workingDayRepository */
        $workingDayRepository = $this->container->get('workingday_repository');
        /** @var ShiftRepository $shiftRepository */
        $shiftRepository = $this->container->get('shift_repository');
        /** @var ShiftTypeRepository $shiftTypeRepository */
        $shiftTypeRepository = $this->container->get('shifttype_repository');
        /** @var DrivingAssertionManagement $drivingAssertionService */
        $drivingAssertionService = $this->container->get('tixi_app.drivingassertionmanagement');

        try {
            $date = new \DateTime();
            $date->setDate($year, $month, 1);
        } catch (\Exception $e) {
            return null;
        }
        $workingMonth = WorkingMonth::registerWorkingMonth($date);
        $workingMonth->createWorkingDaysForThisMonth();

        $shiftTypes = $shiftTypeRepository->findAllActive();

        $workingDays = $workingMonth->getWorkingDays();
        foreach ($workingDays as $workingDay) {
            $workingDayRepository->store($workingDay);
            foreach ($shiftTypes as $shiftType) {
                $shift = Shift::registerShift($workingDay, $shiftType);
                $workingDay->assignShift($shift);
                $shiftRepository->store($shift);
            }

        }
        $workingMonthRepository->store($workingMonth);
        $drivingAssertionService->createAllDrivingAssertionsForNewMonthlyPlan($workingMonth);
        return $workingMonth;
    }

    /**
     * @param MonthlyPlanEditDTO $monthlyPlan
     * @return mixed|void
     */
    public function createDrivingAssertionsFromMonthlyPlan(MonthlyPlanEditDTO $monthlyPlan) {
        /** @var DrivingAssertionManagement $drivingAssertionService */
        $drivingAssertionService = $this->container->get('tixi_app.drivingassertionmanagement');
        $drivingAssertionService->handleMonthlyPlan($monthlyPlan);
    }
}