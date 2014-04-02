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
     * @ORM\ManyToOne(targetEntity="DrivingPool", inversedBy="drivingMissions")
     * @ORM\JoinColumn(name="driving_pool_id", referencedColumnName="id")
     */
    protected $drivingPool;

    public function __construct() {
        $this->drivingOrders = new ArrayCollection();
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
} 