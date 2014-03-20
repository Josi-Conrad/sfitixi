<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 14:14
 */

namespace Tixi\App;


use Tixi\CoreDomain\Vehicle;

interface VehicleManagementService {


    public function getReadyForOperationVehiclesOnDate($date);

    public function addVehicle(Vehicle $vehicle);

    public function changeServicePlanForVehicle();

    public function putVehicleOnService(Vehicle $vehicle, $servicePlan);


} 