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
 * Class AddressManagementControllerTest
 * @package Tixi\App\AppBundle\Tests\Controllers
 */
class AddressManagementControllerTest extends CommonBaseTest {

    public function setUp() {
        parent::setUp();
    }

    public function testGetAddressJSON() {
        $client = $this->createClient();
        $client->request('GET', '/service/address?requeststate=search_state&searchstr=laupen',
            array(), array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'pass',
            ));
        $response = $client->getResponse();
        $json = json_decode($response->getContent());
        foreach ($json->models as $model) {
            $this->assertNotNull($model->street);
        }
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 