<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:06
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\DriverAbsentDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\DriverDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\HandicapDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\InsuranceDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\PoiKeywordDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\VehicleTypeDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\PassengerAbsentDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\PassengerDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\POIDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\RepeatedDrivingAssertionsDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\ServicePlanDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\UserDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridController;

class DataGridControllerFactory extends ContainerAware{

    public function createVehicleController($embeddedState=false, array $routeProperties=array()) {
        return new VehicleDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createServicePlanController($embeddedState=false, array $routeProperties=array()) {
        return new ServicePlanDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createDriverController($embeddedState=false, array $routeProperties=array()) {
        return new DriverDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createDriverAbsentController($embeddedState=false, array $routeProperties=array()) {
        return new DriverAbsentDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createPassengerController($embeddedState=false, array $routeProperties=array()) {
        return new PassengerDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createPassengerAbsentController($embeddedState=false, array $routeProperties=array()) {
        return new PassengerAbsentDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createUserController($embeddedState=false, array $routeProperties=array()) {
        return new UserDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createPOIController($embeddedState=false, array $routeProperties=array()) {
        return new POIDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createRepeatedDrivingAssertionPlanController($embeddedState=false, array $routeProperties=array()) {
        return new RepeatedDrivingAssertionsDataGridController($this->container, $embeddedState, $routeProperties);
    }

    //management
    public function createManagementVehicleTypeController($embeddedState=false, array $routeProperties=array()) {
        return new VehicleTypeDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createManagementPoiKeywordController($embeddedState=false, array $routeProperties=array()) {
        return new PoiKeywordDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createManagementHandicapController($embeddedState=false, array $routeProperties=array()) {
        return new HandicapDataGridController($this->container, $embeddedState, $routeProperties);
    }

    public function createManagementInsuranceController($embeddedState=false, array $routeProperties=array()) {
        return new InsuranceDataGridController($this->container, $embeddedState, $routeProperties);
    }
} 