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
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;

/**
 * Class RideOptimizationCommand
 * @package Tixi\App\AppBundle\Command
 */
class RideOptimizationCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:ride-optimization')
            ->setDescription('Runs ride optimization for all shifts in given day')
            ->addArgument('d', InputArgument::OPTIONAL, 'Day to optimize like: 01.07.2024');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $d = $input->getArgument('d');
        if (!$d) {
            $d = '01.07.2024';
        }
        $date = \DateTime::createFromFormat('d.m.Y', $d);

        $shiftTypeRepo = $this->getContainer()->get('shifttype_repository');
        $rideManagement = $this->getContainer()->get('tixi_app.ridemanagement');
        $dispoManagement = $this->getContainer()->get('tixi_app.dispomanagement');
        $dateTimeService = $this->getContainer()->get('tixi_api.datetimeservice');

        $shifts = array();
        $shiftTypes = $shiftTypeRepo->findAllActive();
        foreach ($shiftTypes as $shiftType) {
            $sstart = $dateTimeService->convertToLocalDateTime($shiftType->getStart());
            $date->setTime($sstart->format('H'), $sstart->format('i'));
            $dayTime = $dateTimeService->convertToLocalDateTime($date);
            $shift = $dispoManagement->getResponsibleShiftForDayAndTime($dayTime);
            if ($shift) {
                $shifts[] = $shift;
            } else {
                $output->writeln('No shift at: ' . $sstart->format('H:i'));
            }
        }

        $startTime = microtime(true);
        foreach ($shifts as $shift) {
            if ($shift) {
                $rideManagement->getOptimizedPlanForShift($shift);
            }
        }
        $endTime = microtime(true);

        $output->writeln('Exectued ' . $d . ' optimization in: ' . ($endTime - $startTime) . "s\n");
    }
}