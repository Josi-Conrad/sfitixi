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
use Tixi\ApiBundle\Tile\TileRenderer;

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
    public static $menuUserId = 'user';
    public static $menuManagementUsersId = 'management_users';

    public static $menuSelectionManagementId = 'management';




    public function __construct() {
        $this->activeMenuId = 'home';
    }

    public function createMenu($activeMenuItem=null) {
        $tileRender = $this->container->get('tixi_api.tilerenderer');
        $activeItem = (null !== $activeMenuItem) ? $activeMenuItem : self::$menuHomeId;
        return $tileRender->render($this->constructMenuTile($activeItem));
    }

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
        //ToDo should only be visible for users with role $$$
        $managementSelectionTile = $menuTile->add(new MenuSelectionItemTile(
            self::$menuSelectionManagementId, 'Management',$this->checkSelectionRootActivity(self::$menuManagementUsersId, $activeItem))
        );
        $managementSelectionTile->add(new MenuItemTile(
            self::$menuVehicleId, $this->generateUrl('tixiapi_vehicles_get'), 'vehicle.panel.name', $this->checkSelectionChildActivity(self::$menuManagementUsersId, $activeItem)));
        return $menuTile;
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    protected function extractRootId($activeItem) {
        return explode('_', $activeItem)[0];
    }

    protected function extractSelectionId($activeItem) {
        $exploded = explode('_', $activeItem);
        $toReturn = '';
        if(count($exploded)>1) {
            $toReturn = $exploded[1];
        }
        return $toReturn;
    }

    protected function checkSelectionRootActivity($menuId, $activeItem) {
        return $this->extractRootId($menuId) === $this->extractRootId($activeItem);
    }

    protected function checkSelectionChildActivity($menuId, $activeItem) {
        return ($this->checkSelectionRootActivity($menuId, $activeItem) &&
            $this->extractSelectionId($menuId) === $this->extractSelectionId($activeItem));
    }
} 