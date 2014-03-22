<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Contradict
 *
 * @ORM\Entity
 * @ORM\Table(name="contradict")
 */
class Contradict {
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Driver", inversedBy="contradicts")
     * @ORM\JoinColumn(name="driver_id", referencedColumnName="id")
     */
    protected $driver;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Passenger", inversedBy="contradicts")
     * @ORM\JoinColumn(name="passenger_id", referencedColumnName="id")
     */
    protected $passenger;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @param $driver
     * @param $passenger
     * @param null $comment
     */
    public function __construct($driver, $passenger, $comment = null) {
        $this->driver = $driver;
        $this->passenger = $passenger;
        $this->comment = $comment;
    }

    public function unlinkAssociations(){
        $this->getPassenger()->getContradicts()->removeElement($this);
        $this->getDriver()->getContradicts()->removeElement($this);
        $this->setDriver(null);
        $this->setPassenger(null);
        $this->setComment(null);
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($driver) {
        $this->driver = $driver;
    }

    /**
     * @return Driver
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @param mixed $passenger
     */
    public function setPassenger($passenger) {
        $this->passenger = $passenger;
    }

    /**
     * @return Passenger
     */
    public function getPassenger() {
        return $this->passenger;
    }

}
