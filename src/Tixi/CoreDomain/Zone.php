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
 * Tixi\CoreDomain\Zone
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ZoneRepositoryDoctrine")
 * @ORM\Table(name="zone")
 */
class Zone extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    protected $rate;

    /**
     * @ORM\OneToMany(targetEntity="ZonePlan", mappedBy="zone")
     */
    protected $zonePlans;

    protected function __construct() {
        $this->zonePlans = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $name
     * @param $rate
     * @return Zone
     */
    public static function registerZone($name, $rate) {
        $zone = new Zone();
        $zone->setName($name);
        $zone->setRate($rate);
        return $zone;
    }

    /**
     * @param null $name
     * @param null $rate
     */
    public function updateZone($name = null, $rate = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($rate)) {
            $this->setRate($rate);
        }
        $this->updateModifiedDate();
    }

    /**
     * @param ZonePlan $zonePlan
     */
    public function assignZonePlan(ZonePlan $zonePlan) {
        $this->zonePlans->add($zonePlan);
    }

    /**
     * @param ZonePlan $zonePlan
     */
    public function removeZonePlan(ZonePlan $zonePlan) {
        $this->zonePlans->remove($zonePlan);
    }

    /**
     * @return mixed
     */
    public function getZonePlans() {
        return $this->zonePlans;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $rate
     */
    public function setRate($rate) {
        $this->rate = $rate;
    }

    /**
     * @return mixed
     */
    public function getRate() {
        return $this->rate;
    }

}