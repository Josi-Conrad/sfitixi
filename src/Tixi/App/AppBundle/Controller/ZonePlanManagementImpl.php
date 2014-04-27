<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 18:58
 */

namespace Tixi\App\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Tixi\App\AppBundle\Util\Point;
use Tixi\App\AppBundle\Util\PolygonCalc;
use Tixi\App\ZonePlanManagement;
use Tixi\CoreDomain\Address;

class ZonePlanManagementImpl extends Controller implements ZonePlanManagement {

    /**
     * returns true if coordinates of an address matches in predefined ZonePlan
     * @param $address
     * @return boolean
     */
    public function addressMatchesZonePlan(Address $address) {
        $zonePlanRepo = $this->get('zoneplan_repository');
        $zone = $zonePlanRepo->find(0);
        $innerZone = $zone->getInnerZone();
        $adjacentZone = $zone->getAdjacentZone();

        $matches = false;
        $polygon = PolygonCalc::createPolygonFromGeoJSON($innerZone);
        $matches = PolygonCalc::pointInPolygon(new Point($address->getLat(), $address->getLng()), $innerZone);

        return $matches;
    }
}