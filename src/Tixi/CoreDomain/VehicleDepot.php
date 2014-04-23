<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.04.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\VehicleDepot
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\VehicleDepotRepositoryDoctrine")
 * @ORM\Table(name="vehicle_depot")
 */
class VehicleDepot extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="depot")
     */
    protected $vehicles;

    protected function __construct() {
        parent::__construct();
        $this->vehicles = new ArrayCollection();
    }

    /**
     * @param $name
     * @param Address $address
     * @return \Tixi\CoreDomain\VehicleDepot
     */
    public static function registerVehicleDepot($name, Address $address) {
        $vehicleDepot = new VehicleDepot();

        $vehicleDepot->setName($name);
        $vehicleDepot->assignAddress($address);

        return $vehicleDepot;
    }

    /**
     * @param null $name
     */
    public function updateVehicleDepotData($name = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
    }

    public function assignAddress(Address $address) {
        $this->address = $address;
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
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getVehicles() {
        return $this->vehicles;
    }

    /**
     * @return mixed
     */
    public function getNameString() {
        return $this->name;
    }
}
