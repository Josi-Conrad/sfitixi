<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 18:58
 */

namespace Tixi\App\AppBundle\ZonePlan;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\AppBundle\ZonePlan\Point;
use Tixi\App\AppBundle\ZonePlan\PolygonCalc;
use Tixi\CoreDomain\Dispo\ZonePlan;
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Zone;

class ZonePlanManagementImpl extends ContainerAware implements ZonePlanManagement {

//    const ZONEPLAN_ID = 0;

    /**
     * returns true if coordinates of an address matches in predefined ZonePlan
     * @param $address
     * @return Zone
     */
    public function getZoneForAddress(Address $address) {
        $zonePlanRepo = $this->container->get('zoneplan_repository');
        /**@var $zone \Tixi\CoreDomain\Zone */
        $zone = $zonePlanRepo->getZonePlanForCityName($address->getCity());
        return $zone;

//        return PolygonCalc::pointInPolygon(new Point($address->getLat(), $address->getLng()),
//            PolygonCalc::createPolygonFromGeoJSON($innerZone));
    }
//
//    /**
//     * returns true if coordinates of an address matches in predefined adjacent ZonePlan
//     * @param $address
//     * @return boolean
//     */
//    public function addressMatchesAdjacentZonePlan(Address $address) {
//        $zonePlanRepo = $this->container->get('zoneplan_repository');
//        /**@var $zone \Tixi\CoreDomain\Dispo\ZonePlan */
//        $zone = $zonePlanRepo->find(0);
//        $adjacentZone = $zone->getAdjacentZone();
//
//        return PolygonCalc::pointInPolygon(new Point($address->getLat(), $address->getLng()),
//            PolygonCalc::createPolygonFromGeoJSON($adjacentZone));
//    }
//
//    /**
//     * @return \Tixi\CoreDomain\Dispo\ZonePlan
//     */
//    public function getZonePlan() {
//        $zonePlanRepo = $this->container->get('zoneplan_repository');
//        $zonePlan = $zonePlanRepo->find(self::ZONEPLAN_ID);
//        if ($zonePlan !== null) {
//            return $zonePlan;
//        } else {
//            return ZonePlan::registerZonePlan('Please insert a geoJSON', 'Please insert a geoJSON');
//        }
//    }
//
//    /**
//     * @param \Tixi\CoreDomain\Dispo\ZonePlan $zonePlan
//     * @return ZonePlan
//     */
//    public function createOrUpdateZonePlan(ZonePlan $zonePlan) {
//        $em = $this->container->get('entity_manager');
//        $zonePlanRepo = $this->container->get('zoneplan_repository');
//        /**@var $zonePlanDB ZonePlan*/
//        $zonePlanDB = $zonePlanRepo->find(self::ZONEPLAN_ID);
//        if ($zonePlanDB !== null) {
//            $zonePlanDB->updateZonePlan($zonePlan->getInnerZone(), $zonePlan->getAdjacentZone());
//            $em->flush();
//            return $zonePlanDB;
//        } else {
//            $zonePlan->setId(self::ZONEPLAN_ID);
//            $zonePlanRepo->store($zonePlan);
//            $em->flush();
//            return $zonePlan;
//        }
//    }
}