<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces;


use Tixi\CoreDomain\ServicePlan;

class ServicePlanAssembler {

    public static function registerServicePlan(ServicePlan $servicePlan, ServicePlanAssignDTO $servicePlanDTO) {
        $servicePlan->registerServicePlan(
            $servicePlanDTO->getStartDate(),
            $servicePlanDTO->getEndDate(),
            $servicePlanDTO->getCost());
        return $servicePlan;
    }

    public static function toDTO(ServicePlan $servicePlan) {
        $servicePlanDTO = new ServicePlanAssignDTO();
        $servicePlanDTO->id = $servicePlan->getId();
        $servicePlanDTO->startDate= $servicePlan->getStartDate();
        $servicePlanDTO->endDate = $servicePlan->getEndDate();
        $servicePlanDTO->cost = $servicePlan->getCost();
        $servicePlanDTO->vehicleId = $servicePlan->getVehicle()->getId();
        return $servicePlanDTO;
    }

    public static function servicePlansToServicePlanListDTOs($servicePlans) {
        $dtoArray = array();
        foreach($servicePlans as $servicePlan) {
            $dtoArray[] = self::toServicePlanListDTO($servicePlan);
        }
        return $dtoArray;
    }

    public static function toServicePlanListDTO(ServicePlan $servicePlan) {
        $servicePlanListDTO = new ServicePlanListDTO();
        $servicePlanListDTO->id = $servicePlan->getId();
        $servicePlanListDTO->startDate = $servicePlan->getStartDate();
        $servicePlanListDTO->endDate = $servicePlan->getEndDate();
        $servicePlanListDTO->cost = $servicePlan->getCost();
        return $servicePlanListDTO;
    }

}