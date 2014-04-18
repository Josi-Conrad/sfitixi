<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:08
 */

namespace Tixi\ApiBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tixi\ApiBundle\Tile\Core\MenuItemTile;
use Tixi\ApiBundle\Tile\Core\MenuSelectionItemTile;
use Tixi\ApiBundle\Tile\Core\MenuTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\TileRenderer;

/**
 * Class MenuService
 * @package Tixi\ApiBundle\Menu
 */
class MenuService extends ContainerAware{

    public static $menuHomeId = 'home';
    public static $menuDispoId = 'dispo';
    public static $menuPrepareId = 'prepare';
    public static $menuPassengerId = 'passenger';
    public static $menuPoiId = 'poi';
    public static $menuDriverId = 'driver';
    public static $menuVehicleId = 'vehicle';
    public static $menuServicePlanId = 'vehicle_serviceplans';
    public static $menuDriverAbsentId = 'driver_absents';
    public static $menuDriverRepeatedAssertionId = 'driver_repeatedassertions';
    public static $menuPassengerAbsentId = 'passenger_absents';
    public static $menuUserProfileId = 'user_profile';

    public static $menuSelectionManagementId = 'management';
    public static $menuManagementUserId = 'management_users';
    public static $menuManagementVehicleCategoryId = 'management_vehiclecategories';
    public static $menuManagementPoiKeywordsId = 'management_poikeywords';
    public static $menuManagementHandicapId = 'management_handicaps';
    public static $menuManagementInsuranceId = 'management_insurances';
    public static $menuManagementBankHolidayId = 'management_bankholidays';
    public static $menuManagementZoningPlanId = 'management_zoningplans';
    public static $menuManagementShiftTypeId = 'management_shifttypes';


    public function __construct() {
        $this->activeMenuId = 'home';
    }

    /**
     * @param null $activeMenuItem
     * @return mixed
     */
    public function createMenu($activeMenuItem=null) {
        $tileRender = $this->container->get('tixi_api.tilerenderer');
        $activeItem = (null !== $activeMenuItem) ? $activeMenuItem : self::$menuHomeId;
        return $tileRender->render($this->constructMenuTile($activeItem));
    }

    /**
     * @param $activeItem
     * @return MenuTile
     */
    protected function constructMenuTile($activeItem) {
        $rootId = $this->extractRootId($activeItem);
        $menuTile = new MenuTile();
        $menuTile->add(new MenuItemTile(self::$menuHomeId, $this->generateUrl('tixiapi_home'), 'home.panel.name', $rootId === self::$menuHomeId));
        $menuTile->add(new MenuItemTile(self::$menuDispoId, '#', 'disposition.panel.name', $rootId === self::$menuDispoId));
        $menuTile->add(new MenuItemTile(self::$menuPrepareId, '#', 'prepare.panel.name', $rootId === self::$menuPrepareId));
        $menuTile->add(new MenuItemTile(self::$menuPassengerId, $this->generateUrl('tixiapi_passengers_get'), 'passenger.panel.name', $rootId === self::$menuPassengerId));
        $menuTile->add(new MenuItemTile(self::$menuPoiId, $this->generateUrl('tixiapi_pois_get'), 'poi.panel.name', $rootId === self::$menuPoiId));
        $menuTile->add(new MenuItemTile(self::$menuDriverId, $this->generateUrl('tixiapi_drivers_get'), 'driver.panel.name', $rootId === self::$menuDriverId));
        $menuTile->add(new MenuItemTile(self::$menuVehicleId, $this->generateUrl('tixiapi_vehicles_get'), 'vehicle.panel.name', $rootId === self::$menuVehicleId));

        /**
         * render management functions only if user got manager role
         */
        if($this->container->get('security.context')->isGranted('ROLE_MANAGER')){
            $managementSelectionTile =
                $menuTile->add(new MenuSelectionItemTile(self::$menuSelectionManagementId,
                'management.panel.name',$this->checkSelectionRootActivity(self::$menuSelectionManagementId, $activeItem)));

            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementUserId,
                $this->generateUrl('tixiapi_management_users_get'), 'user.panel.name', $this->checkSelectionChildActivity(self::$menuManagementUserId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementVehicleCategoryId,
                $this->generateUrl('tixiapi_management_vehiclecategories_get'), 'vehiclecategory.panel.name', $this->checkSelectionChildActivity(self::$menuManagementVehicleCategoryId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementPoiKeywordsId,
                $this->generateUrl('tixiapi_management_poikeywords_get'), 'poikeyword.panel.name', $this->checkSelectionChildActivity(self::$menuManagementPoiKeywordsId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementHandicapId,
                $this->generateUrl('tixiapi_management_handicaps_get'), 'handicap.panel.name', $this->checkSelectionChildActivity(self::$menuManagementHandicapId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementInsuranceId,
                $this->generateUrl('tixiapi_management_insurances_get'), 'insurance.panel.name', $this->checkSelectionChildActivity(self::$menuManagementInsuranceId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementShiftTypeId,
                $this->generateUrl('tixiapi_management_shifttypes_get'), 'shifttype.panel.name', $this->checkSelectionChildActivity(self::$menuManagementShiftTypeId, $activeItem)));
            $managementSelectionTile->add(new MenuItemTile(self::$menuManagementBankHolidayId,
                $this->generateUrl('tixiapi_management_bankholidays_get'), 'bankholiday.panel.name', $this->checkSelectionChildActivity(self::$menuManagementBankHolidayId, $activeItem)));
        }

        return $menuTile;
    }

    /**
     * @param $route
     * @param array $parameters
     * @param bool $referenceType
     * @return string
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @param $activeItem
     * @return mixed
     */
    protected function extractRootId($activeItem) {
        return explode('_', $activeItem)[0];
    }

    /**
     * @param $activeItem
     * @return string
     */
    protected function extractSelectionId($activeItem) {
        $exploded = explode('_', $activeItem);
        $toReturn = '';
        if(count($exploded)>1) {
            $toReturn = $exploded[1];
        }
        return $toReturn;
    }

    /**
     * @param $menuId
     * @param $activeItem
     * @return bool
     */
    protected function checkSelectionRootActivity($menuId, $activeItem) {
        return $this->extractRootId($menuId) === $this->extractRootId($activeItem);
    }

    /**
     * @param $menuId
     * @param $activeItem
     * @return bool
     */
    protected function checkSelectionChildActivity($menuId, $activeItem) {
        return ($this->checkSelectionRootActivity($menuId, $activeItem) &&
            $this->extractSelectionId($menuId) === $this->extractSelectionId($activeItem));
    }
} 