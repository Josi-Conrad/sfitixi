<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 14:46
 */

namespace Tixi\App\Impl\VehicleManagementBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Tixi\App\VehicleManagementService;
use Tixi\CoreDomain\Vehicle;

/**
 * Wird im Moment nicht benötigt - evtl. erweist sich dieses Vorgehen doch als nützlich
 */
class VehicleManagementController implements VehicleManagementService{


    public function getReadyForOperationVehiclesOnDate($date)
    {
        // TODO: Implement getReadyForOperationVehiclesOnDate() method.
    }

    public function addVehicle(Vehicle $vehicle)
    {
        $this->get('vehicle_repository')->store($vehicle);
        $this->getDoctrine()->getManager()->flush();
    }

    public function changeServicePlanForVehicle()
    {

    }

    public function putVehicleOnService(Vehicle $vehicle, $servicePlan)
    {

    }
}