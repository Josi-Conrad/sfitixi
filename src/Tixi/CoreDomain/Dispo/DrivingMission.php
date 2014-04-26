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
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\DrivingMission
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\DrivingMissionRepositoryDoctrine")
 * @ORM\Table(name="driving_mission")
 */
class DrivingMission {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\OneToMany(targetEntity="DrivingOrder", mappedBy="drivingMission")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;
    /**
     * @ORM\ManyToMany(targetEntity="RepeatedDrivingOrder")
     * @ORM\JoinTable(name="drivingmission_to_repeateddrivingorder",
     *      joinColumns={@ORM\JoinColumn(name="divingmission_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="repeateddrivingorder_id", referencedColumnName="id")})
     */
    protected $repeatedDrivingOrders;
    /**
     * @ORM\ManyToOne(targetEntity="DrivingPool", inversedBy="drivingMissions")
     * @ORM\JoinColumn(name="driving_pool_id", referencedColumnName="id")
     */
    protected $drivingPool;

    public function __construct() {
        $this->drivingOrders = new ArrayCollection();
        $this->repeatedDrivingOrders = new ArrayCollection();
    }

    /**
     * returns earliest pickUpType of orders
     * @return \DateTime
     */
    protected function getAnchorTime() {
    }

    /**
     * returns mission duration in tbd(seconds|minutes)
     * @return mixed
     */
    protected function getDuration() {
    }

    /**
     * @param mixed $drivingOrders
     */
    public function setDrivingOrders($drivingOrders) {
        $this->drivingOrders = $drivingOrders;
    }

    /**
     * @return mixed
     */
    public function getDrivingOrders() {
        return $this->drivingOrders;
    }

    /**
     * @param mixed $drivingPool
     */
    public function setDrivingPool($drivingPool) {
        $this->drivingPool = $drivingPool;
    }

    /**
     * @return mixed
     */
    public function getDrivingPool() {
        return $this->drivingPool;
    }

    /**
     * @param mixed $repeatedDrivingOrders
     */
    public function setRepeatedDrivingOrders($repeatedDrivingOrders) {
        $this->repeatedDrivingOrders = $repeatedDrivingOrders;
    }

    /**
     * @return mixed
     */
    public function getRepeatedDrivingOrders() {
        return $this->repeatedDrivingOrders;
    }
}