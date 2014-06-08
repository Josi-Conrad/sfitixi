<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 18:58
 */

namespace Tixi\App\AppBundle\ZonePlan;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\ZonePlan\ZonePlanManagement;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Zone;
use Tixi\CoreDomain\ZonePlan;
use Tixi\CoreDomain\ZonePlanRepository;
use Tixi\CoreDomain\ZoneRepository;

/**
 * Class ZonePlanManagementImpl
 * @package Tixi\App\AppBundle\ZonePlan
 */
class ZonePlanManagementImpl extends ContainerAware implements ZonePlanManagement {
    /**
     * returns true if coordinates of an address matches in predefined ZonePlan
     * @param $address
     * @return Zone|null
     */
    public function getZoneForAddress(Address $address) {
        return $this->getZoneForCity($address->getCity());
    }

    /**
     * @param $city
     * @return null|Zone
     * @throws \InvalidArgumentException
     */
    public function getZoneForCity($city) {
        /** @var ZonePlanRepository $zonePlanRepository */
        $zonePlanRepository = $this->container->get('zoneplan_repository');
        /** @var ZonePlan $zonePlane */
        if (null === $city || $city === '') {
            throw new \InvalidArgumentException();
        }
        $zonePlane = $zonePlanRepository->findZonePlanForCity($city);
        $zone = null;
        /** @var ZonePlan */
        if (null !== $zonePlane) {
            $zone = $zonePlane->getZone();
        } else {
            $zone = $this->findOrCreateUnclassfiedZone();
        }
        return $zone;
    }

    /**
     * @return Zone
     */
    public function findOrCreateUnclassfiedZone() {
        /** @var ZoneRepository $zoneRepository */
        $zoneRepository = $this->container->get('zone_repository');
        $unclassifiedZone = $zoneRepository->findUnclassifiedZone();
        if (null === $unclassifiedZone) {
            $unclassifiedZone = Zone::createUnclassifiedZone();
            $zoneRepository->store($unclassifiedZone);
            $this->container->get('entity_manager')->flush();
        }
        return $unclassifiedZone;
    }

    /**
     * @param $cities
     * @return null|Zone
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function getZoneWithHighestPriorityForCities($cities) {
        /**@var $zoneWithHighestPriority Zone */
        $zoneWithHighestPriority = null;
        foreach ($cities as $city) {
            try {
                $tempZone = $this->getZoneForCity($city);
            } catch (\InvalidArgumentException $e) {
                throw $e;
            }
            if ($zoneWithHighestPriority === null || $tempZone->getPriority() > $zoneWithHighestPriority->getPriority()) {
                $zoneWithHighestPriority = $tempZone;
            }
        }
        return $zoneWithHighestPriority;
    }


    /**
     * @deprecated
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
}