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
use Tixi\CoreDomain\Dispo\DrivingPool;
use Tixi\CoreDomain\Dispo\Shift;
use Tixi\CoreDomain\Dispo\ShiftType;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;

/**
 * Class TestDataDrivingPoolsCommand
 * @package Tixi\App\AppBundle\Command
 */
class TestDataDrivingPoolsCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:testdata-drivingpools')
            ->setDescription('Creates test data for drivingPools, Missions')
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

        $monthDate = new \DateTime('today');
        $monthDate->modify('+' . $month . ' month');
        $monthDate->format('first day of this month');
        if($workingMonthRepo->findWorkingMonthByDate($monthDate)){
            $output->writeln("WorkingMonth " . $monthDate->format('m') . " already exists");
            exit;
        }
        $workingMonth = WorkingMonth::registerWorkingMonth($monthDate);
        $workingMonth->createWorkingDaysForThisMonth();
        foreach ($workingMonth->getWorkingDays() as $wd) {
            $workingDayRepo->store($wd);
        }
        $workingMonthRepo->store($workingMonth);

        $workingDays = $workingMonth->getWorkingDays();
        $shiftTypes = $shiftTypeRepo->findAllNotDeleted();

        //create workingDays shifts, assign them drivingpools, get amount of needed drivers
        /** @var $workingDay WorkingDay */
        foreach ($workingDays as $workingDay) {
            /** @var $shiftType ShiftType */
            foreach ($shiftTypes as $shiftType) {
                $shift = Shift::registerShift($workingDay, $shiftType);
                $shift->setAmountOfDrivers(rand(8, 18));
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

        $drivingPools = array();
        foreach ($workingMonth->getWorkingDays() as $wd) {
            foreach ($wd->getShifts() as $s) {
                foreach ($s->getDrivingPools() as $dp) {

                    array_push($drivingPools, $dp);
                }
            }
        }



        $end = microtime(true);
        $output->writeln("\n--------------------------------------------\n" .
            "Time to store and flush " . count($drivingPools) . " drivingPools: " . ($end - $start) . "s\n");
    }
}