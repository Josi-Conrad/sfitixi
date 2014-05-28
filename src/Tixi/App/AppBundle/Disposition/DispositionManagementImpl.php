<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 16.05.14
 * Time: 15:06
 */

namespace Tixi\App\AppBundle\Disposition;


use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyLeastDistance;
use Tixi\App\AppBundle\Ride\RideStrategies\RideStrategyTimeWindow;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\App\Disposition\DispositionManagement;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftRepository;
use Tixi\CoreDomain\Dispo\ShiftTypeRepository;
use Tixi\CoreDomain\Dispo\WorkingDayRepository;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

class DispositionManagementImpl extends ContainerAware implements DispositionManagement {
    /**
     * @param \DateTime $day
     * @param \DateTime $time
     * @return null|Shift
     */
    public function getResponsibleShiftForDayAndTime(\DateTime $day, \DateTime $time) {
        $timeService = $this->container->get('tixi_api.datetimeservice');
        $shiftRepo = $this->container->get('shift_repository');
        $shiftsForDay = $shiftRepo->findShiftsForDay($day);

        $pickTime = $timeService->convertToLocalDateTime($time);
        $pickMinutes = $timeService->getMinutesOfDay($pickTime);

        foreach ($shiftsForDay as $shift) {
            $startTime = $timeService->convertToLocalDateTime($shift->getStartDate());
            $endTime = $timeService->convertToLocalDateTime($shift->getEndDate());
            $shiftMinutesStart = $timeService->getMinutesOfDay($startTime);
            $shiftMinutesEnd = $timeService->getMinutesOfDay($endTime);
            if ($pickMinutes >= $shiftMinutesStart && $pickMinutes <= $shiftMinutesEnd) {
                return $shift;
            }
        }
        return null;
    }

    /**
     * @param Shift $shift
     * @return DrivingMission[]
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

            //TODO: Tactic if all missions with beginnTime in Shift or also with a part of endTime?
            /** start or end of the order laps into a shift time */
            if ($startMinute >= $shiftMinutesStart && $endMinute <= $shiftMinutesEnd ||
                $startMinute >= $shiftMinutesStart && $startMinute <= $shiftMinutesEnd
            ) {
                array_push($matchingDrivingMissions, $drivingMission);
            }
        }
        return $matchingDrivingMissions;
    }

    /**
     * @param \DateTime $day
     * @return array
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

    public function processChangeInAmountOfDriversPerShift(Shift $shift, $oldAmount, $newAmount) {
        // TODO: Implement processChangeInAmountOfDriversPerShift() method.
        $shift->setAmountOfDrivers($newAmount);
    }

    public function openWorkingMonth($year, $month) {
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->container->get('workingmonth_repository');
        /** @var WorkingDayRepository $workingDayRepository */
        $workingDayRepository = $this->container->get('workingday_repository');
        /** @var ShiftRepository $shiftRepository */
        $shiftRepository = $this->container->get('shift_repository');
        /** @var ShiftTypeRepository $shiftTypeRepository */
        $shiftTypeRepository = $this->container->get('shifttype_repository');

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
        return $workingMonth;
    }
}