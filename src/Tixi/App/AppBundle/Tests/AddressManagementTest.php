<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 26.03.14
 * Time: 11:31
 */

namespace Tixi\App\AppBundle\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomainBundle\Tests\CommonBaseTest;

/**
 * Class AddressManagementTest
 * @package Tixi\App\AppBundle\Tests
 */
class AddressManagementTest extends CommonBaseTest {
    /**
     * Fulltext index only creates on a committed flush on database.
     * For testing purposes we can't use transactions with rollbacks.
     */
    public function setUp() {
        parent::setUp();
    }

    public function testAddressLookup() {
        $addresses = $this->addressRepo->findAddressesWithoutCoordinates();

        $addressService = $this->addressManagement;
        $add = null;
        $count = 0;
        if (count($addresses)) {
            /**@var $address Address */
            foreach ($addresses as $address) {
                $count++;
                echo $address->toString() . ": ";
                $add = $addressService->getAddressInformationByString($address->toString());
                if ($add !== null) {
                    $this->assertNotEmpty($add->lat);
                    echo $add->lat . " " . $add->lng . "\n";
                }
                if ($count == 5) {
                    break;
                }
            }
        }
    }

    public function tearDown() {
        parent::tearDown();
    }
}
 