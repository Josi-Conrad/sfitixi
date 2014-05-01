<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.03.14
 * Time: 11:31
 */

namespace Tixi\App\AppBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\App\AppBundle\Controller\AddressManagementImplDoctrine;
use Tixi\App\AppBundle\Routing\RoutingMachineOSRM;
use Tixi\App\Routing\RouteManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class RoutingServiceTest
 * @package Tixi\App\AppBundle\Tests
 */
class RoutingServiceTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetRouteInformation() {
        $address1 = $this->createTestAddressBaar();
        $address2 = $this->createTestAddressGoldau();

        //for ($i = 0; $i < 100; $i++){
            /**@var $route \Tixi\CoreDomain\Dispo\Route */
            $route = $this->routeManagement->getRouteFromAddresses($address1, $address2);
        //}
        $min = gmdate("H:i:s", $route->getDuration());
        $km = round($route->getDistance() / 1000, 2, PHP_ROUND_HALF_UP);
        echo 'RouteInformation get: ' . "\n";
        echo $km . 'km | ' . $min . "\n";

    }

    public function tearDown() {
        parent::tearDown();
    }
}
 