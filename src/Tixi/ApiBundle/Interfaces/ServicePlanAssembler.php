<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces;


use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\ServicePlan;

class ServicePlanAssembler {

    //injected by service container via setter method
    private $dateTimeService;

    public function registerServicePlan(ServicePlanAssignDTO $servicePlanDTO) {
        $servicePlan = ServicePlan::registerServicePlan(
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->getStartDate()),
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->getEndDate()));
        return $servicePlan;
    }

    public function toServicePlanAssignDTO(ServicePlan $servicePlan) {
        $servicePlanDTO = new ServicePlanAssignDTO();
        $servicePlanDTO->id = $servicePlan->getId();
        $servicePlanDTO->startDate= $this->dateTimeService->convertUTCDateTimeToLocalDateTime($servicePlan->getStartDate());
        $servicePlanDTO->endDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($servicePlan->getEndDate());
        $servicePlanDTO->cost = $servicePlan->getCost();
        $servicePlanDTO->vehicleId = $servicePlan->getVehicle()->getId();
        return $servicePlanDTO;
    }

    public function servicePlansToServicePlanEmbeddedListDTOs($servicePlans) {
        $dtoArray = array();
        foreach($servicePlans as $servicePlan) {
            $dtoArray[] = $this->toServicePlanEmbeddedListDTO($servicePlan);
        }
        return $dtoArray;
    }

    public function toServicePlanEmbeddedListDTO(ServicePlan $servicePlan) {
        $servicePlanEmbeddedListDTO = new ServicePlanEmbeddedListDTO();
        $servicePlanEmbeddedListDTO->id = $servicePlan->getId();
        $servicePlanEmbeddedListDTO->vehicleId = $servicePlan->getVehicle()->getId();
        $servicePlanEmbeddedListDTO->startDate = $this->dateTimeService->convertUTCDateToLocalString($servicePlan->getStartDate());
        $servicePlanEmbeddedListDTO->endDate = $this->dateTimeService->convertUTCDateToLocalString($servicePlan->getEndDate());
        $servicePlanEmbeddedListDTO->cost = $servicePlan->getCost();
        return $servicePlanEmbeddedListDTO;
    }

    public function servicePlansToServicePlanListDTOs($servicePlans) {
        $dtoArray = array();
        foreach($servicePlans as $servicePlan) {
            $dtoArray[] = $this->toServicePlanListDTO($servicePlan);
        }
        return $dtoArray;
    }

    public function toServicePlanListDTO(ServicePlan $servicePlan) {
        $servicePlanListDTO = new ServicePlanListDTO();
        $servicePlanListDTO->id = $servicePlan->getId();
        $servicePlanListDTO->startDate = $servicePlan->getStartDate();
        $servicePlanListDTO->endDate = $servicePlan->getEndDate();
        $servicePlanListDTO->cost = $servicePlan->getCost();
        return $servicePlanListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }

}