<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;

/**
 * Tixi\CoreDomain\Dispo\ZonePlan
 *
 * Only one ZonePlan should exist, we handle ID:0, its relevant only 1 exists with
 * mandatory zone polygons for the disposition and billing system
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\ZonePlanRepositoryDoctrine")
 * @ORM\Table(name="zoneplan")
 */
class ZonePlan {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;
    /**
     * @ORM\Column(type="text")
     */
    protected $innerZone;
    /**
     * @ORM\Column(type="text")
     */
    protected $adjacentZone;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $innerTarif;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $outerTarif;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $adjacentTarif;

    protected function __construct() {

    }

    /**
     * @param $innerZone
     * @param $adjacentZone
     * @return \Tixi\CoreDomain\Dispo\ZonePlan
     */
    public static function registerZonePlan($innerZone, $adjacentZone) {
        $zonePlan = new ZonePlan();
        $zonePlan->setInnerZone($innerZone);
        $zonePlan->setAdjacentZone($adjacentZone);
        return $zonePlan;
    }

    /**
     * @param null $innerZone
     * @param null $adjacentZone
     */
    public function updateZonePlan($innerZone, $adjacentZone) {
        if (!empty($innerZone)) {
            $this->setInnerZone($innerZone);
        }
        if (!empty($adjacentZone)) {
            $this->setAdjacentZone($adjacentZone);
        }
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param mixed $innerZone
     */
    public function setInnerZone($innerZone) {
        $this->innerZone = $innerZone;
    }

    /**
     * @return mixed
     */
    public function getInnerZone() {
        return $this->innerZone;
    }

    /**
     * @param mixed $adjacentZone
     */
    public function setAdjacentZone($adjacentZone) {
        $this->adjacentZone = $adjacentZone;
    }

    /**
     * @return mixed
     */
    public function getAdjacentZone() {
        return $this->adjacentZone;
    }

    /**
     * @param mixed $adjacentTarif
     */
    public function setAdjacentTarif($adjacentTarif) {
        $this->adjacentTarif = $adjacentTarif;
    }

    /**
     * @return mixed
     */
    public function getAdjacentTarif() {
        return $this->adjacentTarif;
    }

    /**
     * @param mixed $innerTarif
     */
    public function setInnerTarif($innerTarif) {
        $this->innerTarif = $innerTarif;
    }

    /**
     * @return mixed
     */
    public function getInnerTarif() {
        return $this->innerTarif;
    }

    /**
     * @param mixed $outerTarif
     */
    public function setOuterTarif($outerTarif) {
        $this->outerTarif = $outerTarif;
    }

    /**
     * @return mixed
     */
    public function getOuterTarif() {
        return $this->outerTarif;
    }
}