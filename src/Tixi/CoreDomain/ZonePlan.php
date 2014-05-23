<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\ZonePlan
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ZonePlanRepositoryDoctrine")
 * @ORM\Table(name="zoneplan")
 */
class ZonePlan extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $city;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    /**
     * @ORM\ManyToOne(targetEntity="Zone", inversedBy="zonePlans")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     */
    protected $zone;

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $city
     * @param null $memo
     * @return ZonePlan
     */
    public static function registerZonePlan($city, $memo = null) {
        $zonePlan = new ZonePlan();
        $zonePlan->setCity($city);
        $zonePlan->setMemo($memo);
        return $zonePlan;
    }

    /**
     * @param null $city
     * @param null $memo
     */
    public function updateZonePlan($city = null, $memo = null) {
        if (!empty($city)) {
            $this->setCity($city);
        }
        if (!empty($memo)) {
            $this->setMemo($memo);
        }
        $this->updateModifiedDate();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param Zone $zone
     */
    public function setZone(Zone $zone) {
        $this->zone = $zone;
        $zone->assignZonePlan($this);
    }

    /**
     * @return Zone
     */
    public function getZone() {
        return $this->zone;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
    }

}