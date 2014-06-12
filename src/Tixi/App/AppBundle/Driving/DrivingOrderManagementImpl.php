<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.06.14
 * Time: 14:04
 */

namespace Tixi\App\AppBundle\Driving;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\Interfaces\DrivingOrderHandleDTO;
use Tixi\App\Driving\DrivingOrderManagement;
use Tixi\CoreDomain\Dispo\DrivingMission;
use Tixi\CoreDomain\Dispo\DrivingMissionRepository;
use Tixi\CoreDomain\Dispo\DrivingOrder;
use Tixi\CoreDomain\Dispo\DrivingOrderRepository;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrder;
use Tixi\CoreDomain\Dispo\RepeatedDrivingOrderPlan;
use Tixi\CoreDomain\Dispo\WorkingDay;
use Tixi\CoreDomain\Dispo\WorkingMonth;
use Tixi\CoreDomain\Dispo\WorkingMonthRepository;

class DrivingOrderManagementImpl extends ContainerAware implements DrivingOrderManagement
{

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function handleNewDrivingOrder(DrivingOrder $drivingOrder)
    {
        /** @var DrivingOrderRepository $drivingOrderRepository */
        $drivingOrderRepository = $this->container->get('drivingorder_repository');
        /** @var DrivingMissionRepository $drivingMissionRepository */
        $drivingMissionRepository = $this->container->get('drivingmission_repository');
        $drivingMission = DrivingMission::registerDrivingMissionFromOrder($drivingOrder);
        $drivingMissionRepository->store($drivingMission);
        $drivingOrderRepository->store($drivingOrder);
    }

    /**
     * @param DrivingOrder $drivingOrder
     * @return mixed|void
     */
    public function handleDeletionOfDrivingOrder(DrivingOrder $drivingOrder)
    {
        /** @var DrivingOrderRepository $drivingOrderRepository */
        $drivingOrderRepository = $this->container->get('drivingorder_repository');
        /** @var DrivingMissionRepository $drivingMissionRepository */
        $drivingMissionRepository = $this->container->get('drivingmission_repository');
        $drivingMission = $drivingOrder->getDrivingMission();
        if (null !== $drivingMission) {
            $drivingMission->deletePhysically();
        }
        $drivingOrder->deletePhysically();
        $drivingMissionRepository->remove($drivingMission);
        $drivingOrderRepository->remove($drivingOrder);
    }

    /**
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     * @return mixed|void
     */
    public function handleNewRepeatedDrivingOrder(RepeatedDrivingOrderPlan $drivingOrderPlan)
    {
        /** @var WorkingMonthRepository $workingMonthRepository */
        $workingMonthRepository = $this->container->get('workingmonth_repository');

        $prospectiveWorkingMonths = $workingMonthRepository->findProspectiveWorkingMonths();
        /** @var WorkingMonth $workingMonth */
        foreach ($prospectiveWorkingMonths as $workingMonth) {
//            $this->handleRepeatedDrivingOrdersForWorkingMonth($drivingOrderPlan, $workingMonth);
        }
    }

    /**
     * @param RepeatedDrivingOrderPlan $drivingOrderPlan
     * @param WorkingMonth $workingMonth
     */
    protected function handleRepeatedDrivingOrdersForWorkingMonth(
        RepeatedDrivingOrderPlan $drivingOrderPlan, WorkingMonth $workingMonth) {
        /** @var BankHolidayRepository $bankHolidayRepository */
        $bankHolidayRepository = $this->container->get('bankholiday_repository');
        $workingDays = $workingMonth->getWorkingDays();
        /** @var WorkingDay $workingDay */
        foreach ($workingDays as $workingDay) {
            $isBankHoliday = $bankHolidayRepository->checkIfWorkingDayIsBankHoliday($workingDay);
            if (!$isBankHoliday || $isBankHoliday == $drivingOrderPlan->getWithHolidays()) {
                $repeatedDrivingOrders = $drivingOrderPlan->getRepeatedDrivingOrdersAsArray();
                /** @var RepeatedDrivingOrder $repeatedDrivingOrder */
                foreach ($repeatedDrivingOrders as $repeatedDrivingOrder) {
                    if ($repeatedDrivingOrder->matching($workingDay->getDate())) {
                        $drivingOrder = DrivingOrder::registerDrivingOrder(
                            $drivingOrderPlan->getPassenger(),
                            $workingDay->getDate(),
                            $repeatedDrivingOrder->getPickUpTime(),
                            $drivingOrderPlan->getCompanion(),
                            $drivingOrderPlan->getMemo(),
                            DrivingOrder::PENDENT,
                            false,
                            $drivingOrderPlan->getAdditionalTime()
                        );
                        $drivingOrder->assignRoute($drivingOrderPlan->getRoute());
                        $drivingOrder->assignRepeatedDrivingOrderPlan($drivingOrderPlan);
                        $drivingOrderPlan->assignDrivingOrder($drivingOrder);
                        $this->handleNewDrivingOrder($drivingOrder);
                    }
                }
            }
        }
    }
}