<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 18:40
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\Route
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RouteRepositoryDoctrine")
 * @ORM\Table(name="route")
 */
class Route {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Address")
     * @ORM\JoinColumn(name="address_start_id", referencedColumnName="id")
     */
    protected $startAddress;
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Address")
     * @ORM\JoinColumn(name="address_target_id", referencedColumnName="id")
     */
    protected $targetAddress;

    /**
     * @ORM\OneToMany(targetEntity="DrivingOrder", mappedBy="route")
     * @ORM\JoinColumn(name="driving_order_id", referencedColumnName="id")
     */
    protected $drivingOrders;
    /**
     * route duration in minutes
     * @ORM\Column(type="integer")
     */
    protected $duration;
    /**
     * distance in m
     * @ORM\Column(type="integer")
     */
    protected $distance;

    public function __construct(){
        $this->drivingOrders = new ArrayCollection();
    }
}