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
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\BankHolidayDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\DriverCategoryDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\HandicapDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\InsuranceDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\PoiKeywordDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\ShiftTypeDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\VehicleCategoryDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\VehicleDepotDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\PassengerAbsentDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\PassengerDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\POIDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\RepeatedDrivingAssertionsDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\ServicePlanDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management\UserDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\VehicleDataGridController;
use Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo\WorkingMonthDataGridController;


/**
 * Class DataGridControllerFactory
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
class DataGridControllerFactory extends ContainerAware {
    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return VehicleDataGridController
     */
    public function createVehicleController($embeddedState = false, array $routeProperties = array()) {
        return new VehicleDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return ServicePlanDataGridController
     */
    public function createServicePlanController($embeddedState = false, array $routeProperties = array()) {
        return new ServicePlanDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return DriverDataGridController
     */
    public function createDriverController($embeddedState = false, array $routeProperties = array()) {
        return new DriverDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return DriverAbsentDataGridController
     */
    public function createDriverAbsentController($embeddedState = false, array $routeProperties = array()) {
        return new DriverAbsentDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return PassengerDataGridController
     */
    public function createPassengerController($embeddedState = false, array $routeProperties = array()) {
        return new PassengerDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return PassengerAbsentDataGridController
     */
    public function createPassengerAbsentController($embeddedState = false, array $routeProperties = array()) {
        return new PassengerAbsentDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return UserDataGridController
     */
    public function createUserController($embeddedState = false, array $routeProperties = array()) {
        return new UserDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return POIDataGridController
     */
    public function createPOIController($embeddedState = false, array $routeProperties = array()) {
        return new POIDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return RepeatedDrivingAssertionsDataGridController
     */
    public function createRepeatedDrivingAssertionPlanController($embeddedState = false, array $routeProperties = array()) {
        return new RepeatedDrivingAssertionsDataGridController($this->container, $embeddedState, $routeProperties);
    }


    /**
     * Management
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return VehicleCategoryDataGridController
     */
    public function createManagementVehicleTypeController($embeddedState = false, array $routeProperties = array()) {
        return new VehicleCategoryDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return PoiKeywordDataGridController
     */
    public function createManagementPoiKeywordController($embeddedState = false, array $routeProperties = array()) {
        return new PoiKeywordDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return HandicapDataGridController
     */
    public function createManagementHandicapController($embeddedState = false, array $routeProperties = array()) {
        return new HandicapDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return InsuranceDataGridController
     */
    public function createManagementInsuranceController($embeddedState = false, array $routeProperties = array()) {
        return new InsuranceDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return ShiftTypeDataGridController
     */
    public function createManagementShiftTypeController($embeddedState = false, array $routeProperties = array()) {
        return new ShiftTypeDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return BankHolidayDataGridController
     */
    public function createManagementBankHolidayController($embeddedState = false, array $routeProperties = array()) {
        return new BankHolidayDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return VehicleDepotDataGridController
     */
    public function createManagementVehicleDepotController($embeddedState = false, array $routeProperties = array()) {
        return new VehicleDepotDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return DriverCategoryDataGridController
     */
    public function createManagementDriverCategoryController($embeddedState = false, array $routeProperties = array()) {
        return new DriverCategoryDataGridController($this->container, $embeddedState, $routeProperties);
    }

    /**
     * @param bool $embeddedState
     * @param array $routeProperties
     * @return WorkingMonthDataGridController
     */
    public function createDispoWorkingMonthController($embeddedState = false, array $routeProperties = array()) {
        return new WorkingMonthDataGridController($this->container, $embeddedState, $routeProperties);
    }
} 