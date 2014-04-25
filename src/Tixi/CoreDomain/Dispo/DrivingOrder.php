<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 14:38
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\DrivingOrder
 *
 * @ORM\Entity
 * @ORM\Table(name="driving_order")
 */

class DrivingOrder extends CommonBaseEntity implements DrivingOrderInterface{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="text")
     */
    protected $memo;
    /**
     * @ORM\Column(type="date")
     */
    protected $pickUpDate;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $pickUpTime;
    /**
     * @ORM\ManyToOne(targetEntity="Route")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id")
     */
    protected $route;
    /**
     * @ORM\ManyToOne(targetEntity="Tixi\CoreDomain\Passenger", inversedBy="drivingOrders")
     * @ORM\JoinColumn(name="passenger_id", referencedColumnName="id")
     */
    protected $passenger;

    public function matching(\DateTime $date)
    {
        // TODO: Implement matching() method.
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $passenger
     */
    public function setPassenger($passenger)
    {
        $this->passenger = $passenger;
    }

    /**
     * @return mixed
     */
    public function getPassenger()
    {
        return $this->passenger;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }


}