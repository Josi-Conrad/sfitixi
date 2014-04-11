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

    //
    /**
     * injected by service container via setter method
     * @var $dateTimeService DateTimeService
     */
    private $dateTimeService;

    /**
     * @param ServicePlanRegisterDTO $servicePlanDTO
     * @return ServicePlan
     */
    public function registerDTOtoNewServicePlan(ServicePlanRegisterDTO $servicePlanDTO) {
        $servicePlan = ServicePlan::registerServicePlan(
            $servicePlanDTO->startDate,
            $servicePlanDTO->endDate,
            $servicePlanDTO->memo
        );
        return $servicePlan;
    }

    /**
     * @param ServicePlanRegisterDTO $servicePlanDTO
     * @param ServicePlan $servicePlan
     * @return ServicePlan
     */
    public function registerDTOtoServicePlan(ServicePlanRegisterDTO $servicePlanDTO, ServicePlan $servicePlan) {
        $servicePlan->updateBasicData(
            $servicePlanDTO->startDate,
            $servicePlanDTO->endDate,
            $servicePlanDTO->memo
        );
        return $servicePlan;
    }

    /**
     * @param ServicePlan $servicePlan
     * @return ServicePlanRegisterDTO
     */
    public function toServicePlanRegisterDTO(ServicePlan $servicePlan) {
        $servicePlanDTO = new ServicePlanRegisterDTO();
        $servicePlanDTO->id = $servicePlan->getId();
        $servicePlanDTO->startDate =
            $this->dateTimeService->convertToLocalDateTime($servicePlan->getStartDate());
        $servicePlanDTO->endDate =
            $this->dateTimeService->convertToLocalDateTime($servicePlan->getEndDate());
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
        $servicePlanEmbeddedListDTO->startDate =
            $this->dateTimeService->convertToLocalDateTimeString($servicePlan->getStartDate());
        $servicePlanEmbeddedListDTO->endDate =
            $this->dateTimeService->convertToLocalDateTimeString($servicePlan->getEndDate());
        $servicePlanEmbeddedListDTO->memo = $servicePlan->getMemo();
        return $servicePlanEmbeddedListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }

}