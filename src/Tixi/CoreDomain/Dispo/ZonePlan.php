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
     * @ORM\Column(type="integer")
     */
    protected $tarif;
    /**
     * @ORM\Column(type="text")
     */
    protected $innerZone;
    /**
     * @ORM\Column(type="text")
     */
    protected $adjacentZone;

    protected function __construct() {
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
     * @return mixed
     */
    public function getId() {
        return $this->id;
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
}