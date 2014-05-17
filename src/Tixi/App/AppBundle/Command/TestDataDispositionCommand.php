<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 17:53
 */

namespace Tixi\App\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\POI;

/**
 * Class TestDataDispositionCommand
 * @package Tixi\App\AppBundle\Command
 */
class TestDataDispositionCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:testdata')
            ->setDescription('Creates test data for Pools, Orders, Missions')
            ->addArgument('month', InputArgument::OPTIONAL, 'Set Months ago from today to create DrivingPools in workingMonth');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $start = microtime(true);

        $month = $input->getArgument('month');
        if (!$month) {
            $month = 1;
        }

        $em = $this->getContainer()->get('entity_manager');
        $workingMonthRepo = $this->getContainer()->get('workingmonth_repository');
        $workingDayRepo = $this->getContainer()->get('workingday_repository');
        $shiftRepo = $this->getContainer()->get('shift_repository');
        $shiftTypeRepo = $this->getContainer()->get('shifttype_repository');
        $drivingPoolRepo = $this->getContainer()->get('drivingpool_repository');
        $passengerRepo = $this->getContainer()->get('passenger_repository');
        $driverRepo = $this->getContainer()->get('driver_repository');
        $vehicleRepo = $this->getContainer()->get('vehicle_repository');
        $addressRepo = $this->getContainer()->get('address_repository');
        $poiRepo = $this->getContainer()->get('poi_repository');
        $routeRepo = $this->getContainer()->get('route_repository');
        $drivingMissionRepo = $this->getContainer()->get('drivingmission_repository');
        $drivingOrderRepo = $this->getContainer()->get('drivingorder_repository');
        $reDrivingOrderRepo = $this->getContainer()->get('repeateddrivingorder_repository.doctrine');
        $reDrivingOrderPlanRepo = $this->getContainer()->get('repeateddrivingorderplan_repository.doctrine');

        $time = $this->getContainer()->get('tixi_api.datetimeservice');
        $routeManagement = $this->getContainer()->get('tixi_app.routemanagement');

        $monthDate = new \DateTime('today');
        $monthDate->modify('+' . $month . ' month');
        $monthDate->modify('first day of this month');

        $shiftTypes = $shiftTypeRepo->findAllNotDeleted();

        $workingMonth = $workingMonthRepo->findWorkingMonthByDate($monthDate);
        if ($workingMonth !== null) {
            $output->writeln("WorkingMonth " . $monthDate->format('m') . " already exists");
        } else {
            $workingMonth = WorkingMonth::registerWorkingMonth($monthDate);
            $workingMonth->createWorkingDaysForThisMonth();
            foreach ($workingMonth->getWorkingDays() as $wd) {
                $workingDayRepo->store($wd);
            }
            $workingMonthRepo->store($workingMonth);

            $workingDays = $workingMonth->getWorkingDays();

            //create workingDays shifts, assign them drivingpools, get amount of needed drivers
            /** @var $workingDay WorkingDay */
            foreach ($workingDays as $workingDay) {
                /** @var $shiftType ShiftType */
                foreach ($shiftTypes as $shiftType) {
                    $shift = Shift::registerShift($workingDay, $shiftType);
                    $shift->setAmountOfDrivers(rand(12, 20));
                    $workingDay->assignShift($shift);
                    for ($i = 1; $i <= $shift->getAmountOfDrivers(); $i++) {
                        $drivingPool = DrivingPool::registerDrivingPool($shift);
                        $shift->assignDrivingPool($drivingPool);
                        $drivingPoolRepo->store($drivingPool);
                    }
                    $shiftRepo->store($shift);
                }
                $workingDayRepo->store($workingDay);
            }
            $em->flush();
        }
        $drivingPools = array();
        foreach ($workingMonth->getWorkingDays() as $wd) {
            foreach ($wd->getShifts() as $s) {
                foreach ($s->getDrivingPools() as $dp) {
                    array_push($drivingPools, $dp);
                }
            }
        }

        //create Driving Orders
        $countOrders = 0;
        foreach ($shiftTypes as $shiftType) {
            $approxOrdersPerShift = rand(40, 60);
            for ($i = 0; $i < $approxOrdersPerShift; $i++) {
                /**@var $passenger Passenger */
                $passenger = $passengerRepo->find(rand(100, 500));
                $passenger->setIsInWheelChair(rand(0,1));

                /**  WARNING: saving times in UTC on database, but minutesOfDay are from midnight, causing
                 * wrong data if a time is UTC 23:30 but CET 00:30 (= 30 minutesOfDay)
                 */
                $stStart = $time->convertToLocalDateTime($shiftType->getStart());
                $stEnd = $time->convertToLocalDateTime($shiftType->getEnd());

                $stDuration = $stStart->diff($stEnd);
                $minutes = ($stDuration->h * 60 + $stDuration->i);

                $pickupTime = clone $stStart;
                $pickupTime->add(new \DateInterval('PT' . rand(1, $minutes) . 'M'));

                //DrivingOrder <-> Passenger + Route
                $order = DrivingOrder::registerDrivingOrder($monthDate, $pickupTime, rand(0, 1));

                $start = $passenger->getAddress();
                $target = $addressRepo->find(rand(10, 1000));
                $route = $routeManagement->getRouteFromAddresses($start, $target);

                $order->assignRoute($route);
                $order->assignPassenger($passenger);
                $passenger->assignDrivingOrder($order);
                $drivingOrderRepo->store($order);

                $wMin = DispositionVariables::BOARDING_TIME + DispositionVariables::DEBOARDING_TIME;
                $eMin = $passenger->getExtraMinutes();
                $rMin = $route->getAdditionalTime();
                $serviceDuration = $route->getDurationInMinutes() + $wMin + $eMin + $rMin;

                $serviceMinuteOfDay = $time->getMinutesOfDay($pickupTime);

                //DrivingMission <-> Order
                $drivingMission = DrivingMission::registerDrivingMission(rand(0, 1), $serviceMinuteOfDay, $serviceDuration, $route->getDistanceInMeters());
                $drivingMission->assignDrivingOrder($order);
                $order->assignDrivingMission($drivingMission);
                $drivingMissionRepo->store($drivingMission);

                $countOrders++;
            }
        }

        $end = microtime(true);
        $output->writeln(
            "\n--------------------------------------------\n" .
            "Testdata created for: " . $monthDate->format('d.m.Y') . "\n" .
            "with " . $countOrders . " DrivingOrders \n"
        );
    }
}