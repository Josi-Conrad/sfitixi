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
use Tixi\ApiBundle\Helper\WeekdayService;
use Tixi\App\Disposition\DispositionVariables;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\RepeatedWeeklyDrivingAssertion;
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
            ->setDescription('Creates test data for a month with Pools, Orders, and first day of month with Missions')
            ->addArgument('month', InputArgument::OPTIONAL, 'Set Months ago from today to create DrivingPools in workingMonth');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {

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
        $repeatedDrivingAssertionRepo = $this->getContainer()->get('repeateddrivingassertion_repository');
        $repeatedDrivingAssertionPlanRepo = $this->getContainer()->get('repeateddrivingassertionplan_repository');
        $repeatedDrivingOrderRepo = $this->getContainer()->get('repeateddrivingorder_repository.doctrine');
        $repeatedDrivingOrderPlanRepo = $this->getContainer()->get('repeateddrivingorderplan_repository.doctrine');

        $time = $this->getContainer()->get('tixi_api.datetimeservice');
        $routingMachine = $this->getContainer()->get('tixi_app.routingmachine');
        $routeManagement = $this->getContainer()->get('tixi_app.routemanagement');
        $dispoManagement = $this->getContainer()->get('tixi_app.dispomanagement');

        $monthDate = new \DateTime('today');
        $monthDate->modify('+' . $month . ' month');
        $monthDate->modify('+ 10 year');
        $monthDate->modify('first day of this month');

        $shiftTypes = $shiftTypeRepo->findAllActive();

        $drivers = $driverRepo->findAllActive();
        foreach ($drivers as $driver) {
            //no other assertion for zivis
            if ($driver->getDriverCategory()->getId() == 2) {
                continue;
            }
            $reDrivingAssertionPlan = RepeatedDrivingAssertionPlan::registerRepeatedAssertionPlan(
                'test', new \DateTime('today'), 'weekly', rand(0, 1));
            $reDrivingAssertionPlan->assignDriver($driver);
            $driver->assignRepeatedDrivingAssertionPlan($reDrivingAssertionPlan);
            $repeatedDrivingAssertionPlanRepo->store($reDrivingAssertionPlan);
            for ($i = 1; $i <= 7; $i++) {
                if (rand(0, 3) < 3) {
                    $reDrivingWeeklyAssertion = new RepeatedWeeklyDrivingAssertion();
                    $reDrivingWeeklyAssertion->addShiftType($shiftTypes[rand(0, count($shiftTypes) - 1)]);
                    $reDrivingWeeklyAssertion->addShiftType($shiftTypes[rand(0, count($shiftTypes) - 1)]);
                    $reDrivingWeeklyAssertion->addShiftType($shiftTypes[rand(0, count($shiftTypes) - 1)]);
                    $reDrivingWeeklyAssertion->setWeekday($i);
                    $reDrivingWeeklyAssertion->setAssertionPlan($reDrivingAssertionPlan);
                    $reDrivingAssertionPlan->assignRepeatedDrivingAssertion($reDrivingWeeklyAssertion);
                    $repeatedDrivingAssertionRepo->store($reDrivingWeeklyAssertion);
                }
            }
        }
        $em->flush();

        $drivingPools = 0;
        $workingMonth = $workingMonthRepo->findWorkingMonthByDate($monthDate);
        if ($workingMonth !== null) {
            $output->writeln("WorkingMonth " . $monthDate->format('m') . " already exists");
        } else {
            $workingMonth = $dispoManagement->openWorkingMonth($monthDate->format('Y'), $monthDate->format('m'));
            $workingDays = $workingMonth->getWorkingDays();

            //create workingDays shifts, assign them drivingpools, get amount of needed drivers
            /** @var $workingDay WorkingDay */
            foreach ($workingDays as $workingDay) {
                /** @var $shiftType ShiftType */
                foreach ($workingDay->getShifts() as $shift) {
                    $shift->setAmountOfDrivers(rand(12, 18));
                    for ($i = 1; $i <= $shift->getAmountOfDrivers(); $i++) {
                        $drivingPool = DrivingPool::registerDrivingPool($shift);
                        $shift->assignDrivingPool($drivingPool);
                        $drivingPoolRepo->store($drivingPool);
                        $drivingPools++;
                    }
                }
            }
        }
        $em->flush();

        /**@var $pois POI[] */
        $pois = $poiRepo->findAll();
        $countPois = count($pois);

        $orders = array();
        $routes = array();

        //create Driving Orders
        $countOrders = 0;
        foreach ($shiftTypes as $shiftType) {
            $approxOrdersPerShift = rand(40, 60);
            for ($i = 0; $i < $approxOrdersPerShift; $i++) {
                /**@var $passenger Passenger */
                $passenger = $passengerRepo->find(rand(100, 500));
                $passenger->setIsInWheelChair(rand(0, 1));

                /**  WARNING: saving times in UTC on database, but minutesOfDay are from midnight, causing
                 * wrong data if a time is UTC 23:30 but CET 00:30 (= 30 minutesOfDay)
                 */
                $stStart = $time->convertToLocalDateTime($shiftType->getStart());
                $stEnd = $time->convertToLocalDateTime($shiftType->getEnd());

                $stDuration = $stStart->diff($stEnd);
                $minutes = ($stDuration->h * 60 + $stDuration->i);

                $pickupTime = clone $stStart;
                $pickupTime->add(new \DateInterval('PT' . rand(1, $minutes) . 'M'));

                $order = DrivingOrder::registerDrivingOrder($passenger, $monthDate, $pickupTime, rand(0, 1), null, 0, 0, 1);

                $start = $passenger->getAddress();
                $target = $pois[rand(0, $countPois - 1)]->getAddress();
                $route = Route::registerRoute($start, $target);
                $hashKey = hash('crc32', $start->getHashFromBigIntCoordinates() . $target->getHashFromBigIntCoordinates());
                $routes[$hashKey] = $route;
                $route = $routeRepo->storeRouteIfNotExist($route);

                $order->assignRoute($route);
                $order->assignPassenger($passenger);
                $passenger->assignDrivingOrder($order);
                $drivingOrderRepo->store($order);
                array_push($orders, $order);
            }
        }

        $routingMachine->fillRoutingInformationForMultipleRoutes($routes);

        /**@var $order DrivingOrder */
        foreach ($orders as $order) {
            $passenger = $order->getPassenger();
            $route = $order->getRoute();

            $boardingTime = DispositionVariables::BOARDING_TIME + DispositionVariables::DEBOARDING_TIME;
            $extraMinutesPassenger = $passenger->getExtraMinutes();
            $additionalTimesOnRide = $boardingTime + $extraMinutesPassenger;

            $serviceMinuteOfDay = $time->getMinutesOfDay($order->getPickUpTime());
            $serviceDuration = $route->getDurationInMinutes() + $additionalTimesOnRide;
            $serviceDistance = $route->getDistanceInMeters();

            //DrivingMission <-> Order
            $drivingMission = DrivingMission::registerDrivingMission(rand(0, 1), $serviceMinuteOfDay, $serviceDuration, $serviceDistance);
            $drivingMission->assignDrivingOrder($order);
            $order->assignDrivingMission($drivingMission);
            $drivingMissionRepo->store($drivingMission);

            $countOrders++;
        }
        $em->flush();

        $output->writeln(
            "\n--------------------------------------------\n" .
            "Testdata created for month: " . $monthDate->format('m.Y') . " with:\n"
            . $drivingPools . " DrivingPools \n" .
            "And orders for one day: " . $monthDate->format('d.m.Y') . " with:\n"
            . $countOrders . " DrivingOrders and Routes \n"
        );
    }
}