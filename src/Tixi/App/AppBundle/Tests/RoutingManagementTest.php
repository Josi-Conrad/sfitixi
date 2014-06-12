<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.03.14
 * Time: 11:31
 */

namespace Tixi\App\AppBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\App\AppBundle\Routing\RoutingMachineOSRM;
use Tixi\App\Routing\RouteManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class RoutingManagementTest
 * @package Tixi\App\AppBundle\Tests
 */
class RoutingManagementTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetSingleRouteInformation() {
        $address1 = $this->addressRepo->find('10');
        if($address1 === null){
            $address1 = $this->createTestAddressBaar();
        }
        $address2 = $this->addressRepo->find('20');
        if($address2 === null){
            $address2 = $this->createTestAddressGoldau();
        }
        $route = $this->routeManagement->getRouteFromAddresses($address1, $address2);
        $this->assertNotEmpty($route->getDurationInMinutes());
    }

    public function testGetMultipleRouteInformations() {
        $address1 = $this->createTestAddressBaar();
        $address2 = $this->createTestAddressGoldau();

        $routes = array();
        for ($i = 0; $i < 100; $i++) {
            array_push($routes, Route::registerRoute($address1, $address2, null, null));
            array_push($routes, Route::registerRoute($address2, $address1, null, null));
        }
        $s = microtime(true);
        $this->routingMachine->fillRoutingInformationForMultipleRoutes($routes);
        $e = microtime(true);
        echo "\n\nFilled " . count($routes) . " routes from RoutingMachine in: " . ($e - $s) . "s\n";

        /**@var $route Route */
        foreach ($routes as $route) {
            $this->assertNotEmpty($route->getDurationInMinutes());
        }
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 