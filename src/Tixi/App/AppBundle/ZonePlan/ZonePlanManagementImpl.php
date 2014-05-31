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
    /**
     * returns true if coordinates of an address matches in predefined ZonePlan
     * @param $address
     * @return Zone|null
     */
    public function getZoneForAddress(Address $address) {
        return $this->getZoneForAddressData($address->getCity(), $address->getPostalCode());
    }

    /**
     * returns zone which matches city or plz pattern
     * @param $city
     * @param $plz
     * @return Zone
     */
    public function getZoneForAddressData($city, $plz) {
        $zonePlanRepo = $this->container->get('zoneplan_repository');
        $zonePlans = $zonePlanRepo->getZonePlanForAddressData($city, $plz);

        if ($zonePlans) {
            foreach ($zonePlans as $zonePlan) {
                $zone = $zonePlan->getZone();
                //same city found
                if ($zonePlan->getCity() === $city) {
                    return $zone;
                }
                //if not city, then compare PLZ substring
                $zonePlz = $zonePlan->getPostalCode();
                $plzCompareZone = rtrim($zonePlz, '*');
                $trims = strlen($zonePlz) - strlen($plzCompareZone);
                $plzCompare = substr($plz, 0, -$trims);
                if ($plzCompareZone == $plzCompare) {
                    return $zone;
                }
            }
        }
        return null;
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
    public function getZoneForCity($city)
    {
        /** @var ZonePlanRepository $zonePlanRepository */
        $zonePlanRepository = $this->container->get('zoneplan_repository');
        /** @var ZonePlan $zonePlane */
        $zonePlane = $zonePlanRepository->getZonePlanForCity($city);
        $zone = null;
        /** @var ZonePlan */
        if(null !== $zonePlane) {
            $zone = $zonePlane->getZone();
        }
        return $zone;
    }
}