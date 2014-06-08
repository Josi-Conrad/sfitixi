<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.03.14
 * Time: 11:31
 */

namespace Tixi\App\AppBundle\Tests\Controllers;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class RoutingManagementControllerTest
 * @package Tixi\App\AppBundle\Tests\Controllers
 */
class RoutingManagementControllerTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetRouteJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/route?latFrom=47.498796&lngFrom=7.760499&latTo=47.049796&lngTo=8.548057',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        $status = $json->status;
        $this->assertEquals('0', $status);
        $rd = $json->routeDuration;
        $this->assertNotEmpty($rd);
    }

    public function testGetRoutingJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/routing?latFrom=47.498796&lngFrom=7.760499&latTo=47.049796&lngTo=8.548057',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        $status = $json->status;
        $this->assertEquals('0', $status);
        $rd = $json->routeOutwardDuration;
        $this->assertNotNull($rd);
        $rr = $json->routeReturnDuration;
        $this->assertNotNull($rr);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 