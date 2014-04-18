<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 13.03.14
 * Time: 18:10
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\VehicleCategoryRepositoryDoctrine")
 * @ORM\Table(name="vehicle_category")
 */
class VehicleCategory extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $amountOfSeats;

    /**
     * @ORM\Column(type="integer")
     */
    protected $amountOfWheelChairs;

    protected  function __construct() {
        parent::__construct();
    }

    public static function registerVehicleCategory($name, $amountOfSeats, $amountOfWheelChairs = 0) {
        $vehicleCategory = new VehicleCategory();

        $vehicleCategory->setName($name);
        $vehicleCategory->setAmountOfSeats($amountOfSeats);
        $vehicleCategory->setAmountOfWheelChairs($amountOfWheelChairs);

        return $vehicleCategory;
    }

    /**
     * @param null $name
     * @param null $amountOfSeats
     * @param null $amountOfWheelChairs
     */
    public function updateData($name=null, $amountOfSeats=null, $amountOfWheelChairs=null) {
        if(null!==$name) {
            $this->name=$name;
        }
        if(null !== $amountOfSeats) {
            $this->amountOfSeats = $amountOfSeats;
        }
        if(null !== $amountOfWheelChairs) {
            $this->amountOfWheelChairs = $amountOfWheelChairs;
        }
        $this->updateModifiedDate();
    }

    /**
     * @param mixed $amountOfSeats
     */
    public function setAmountOfSeats($amountOfSeats) {
        $this->amountOfSeats = $amountOfSeats;
    }

    /**
     * @return mixed
     */
    public function getAmountOfSeats() {
        return $this->amountOfSeats;
    }

    /**
     * @param mixed $amountOfWheelChairs
     */
    public function setAmountOfWheelChairs($amountOfWheelChairs) {
        $this->amountOfWheelChairs = $amountOfWheelChairs;
    }

    /**
     * @return mixed
     */
    public function getAmountOfWheelChairs() {
        return $this->amountOfWheelChairs;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
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
}