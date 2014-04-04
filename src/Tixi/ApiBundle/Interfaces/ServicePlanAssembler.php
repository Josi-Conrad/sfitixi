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

    /**
     * @param ServicePlanAssignDTO $servicePlanDTO
     * @return ServicePlan
     */
    public function registerDTOtoNewServicePlan(ServicePlanAssignDTO $servicePlanDTO) {
        $servicePlan = ServicePlan::registerServicePlan(
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->startDate),
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->endDate),
            $servicePlanDTO->memo
        );
        return $servicePlan;
    }

    /**
     * @param ServicePlanAssignDTO $servicePlanDTO
     * @param ServicePlan $servicePlan
     * @return ServicePlan
     */
    public function registerDTOtoServicePlan(ServicePlanAssignDTO $servicePlanDTO, ServicePlan $servicePlan) {
        $servicePlan->updateBasicData(
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->startDate),
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($servicePlanDTO->endDate),
            $servicePlanDTO->memo
        );
        return $servicePlan;
    }

    /**
     * @param ServicePlan $servicePlan
     * @return ServicePlanAssignDTO
     */
    public function toServicePlanAssignDTO(ServicePlan $servicePlan) {
        $servicePlanDTO = new ServicePlanAssignDTO();
        $servicePlanDTO->id = $servicePlan->getId();
        $servicePlanDTO->startDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($servicePlan->getStartDate());
        $servicePlanDTO->endDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($servicePlan->getEndDate());
        $servicePlanDTO->memo = $servicePlan->getMemo();
        $servicePlanDTO->vehicleId = $servicePlan->getVehicle()->getId();
        return $servicePlanDTO;
    }

    /**
     * @param $servicePlans
     * @return array
     */
    public function servicePlansToServicePlanEmbeddedListDTOs($servicePlans) {
        $dtoArray = array();
        foreach ($servicePlans as $servicePlan) {
            $dtoArray[] = $this->toServicePlanEmbeddedListDTO($servicePlan);
        }
        return $dtoArray;
    }

    /**
     * @param ServicePlan $servicePlan
     * @return ServicePlanEmbeddedListDTO
     */
    public function toServicePlanEmbeddedListDTO(ServicePlan $servicePlan) {
        $servicePlanEmbeddedListDTO = new ServicePlanEmbeddedListDTO();
        $servicePlanEmbeddedListDTO->id = $servicePlan->getId();
        $servicePlanEmbeddedListDTO->vehicleId = $servicePlan->getVehicle()->getId();
        $servicePlanEmbeddedListDTO->startDate = $this->dateTimeService->convertUTCDateTimeToLocalString($servicePlan->getStartDate());
        $servicePlanEmbeddedListDTO->endDate = $this->dateTimeService->convertUTCDateTimeToLocalString($servicePlan->getEndDate());
        $servicePlanEmbeddedListDTO->memo = $servicePlan->getMemo();
        return $servicePlanEmbeddedListDTO;
    }

    /**
     * @param $servicePlans
     * @return array
     */
    public function servicePlansToServicePlanListDTOs($servicePlans) {
        $dtoArray = array();
        foreach ($servicePlans as $servicePlan) {
            $dtoArray[] = $this->toServicePlanListDTO($servicePlan);
        }
        return $dtoArray;
    }

    /**
     * @param ServicePlan $servicePlan
     * @return ServicePlanListDTO
     */
    public function toServicePlanListDTO(ServicePlan $servicePlan) {
        $servicePlanListDTO = new ServicePlanListDTO();
        $servicePlanListDTO->id = $servicePlan->getId();
        $servicePlanListDTO->startDate = $servicePlan->getStartDate();
        $servicePlanListDTO->endDate = $servicePlan->getEndDate();
        $servicePlanListDTO->memo = $servicePlan->getMemo();
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