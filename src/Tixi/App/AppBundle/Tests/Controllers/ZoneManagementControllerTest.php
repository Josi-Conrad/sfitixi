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
class ZoneManagementControllerTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetZoneJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/zone',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();

        $json = json_decode($response->getContent());
        $status = $json->status;
        $this->assertNotEmpty($status);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 