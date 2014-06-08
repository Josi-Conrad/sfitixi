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
 * Class RideManagementControllerTest
 * @package Tixi\App\AppBundle\Tests\Controllers
 */
class RideManagementControllerTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetFeasibleJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/ride/feasible?day=01.07.2024&time=12:23&direction=1&duration=23&additionalTime=2',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        if($json){
            $feas = $json->isFeasible;
            $this->assertNotNull($feas);
        } else {
            $this->assertNotNull($json);
        }
    }

    public function testGetRepeatedFeasibleJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/ride/repeatedFeasible?fromDate=01.06.2014&toDate=01.07.2025&weekday=1&time=12:23&direction=1&duration=23&additionalTime=2',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        if($json){
            $feas = $json->isFeasible;
            $this->assertNotNull($feas);
        } else {
            $this->assertNotNull($json);
        }
    }

    public function testGetOptimizeJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/ride/optimize?shiftId=1',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        if($json){
            $success = $json->success;
            $this->assertNotNull($success);
        } else {
            $this->assertNotNull($json);
        }
    }

    public function tearDown() {
        parent::tearDown();
    }

}
 