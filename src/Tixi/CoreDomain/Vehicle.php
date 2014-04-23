<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 21.02.14
 * Time: 13:01
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Vehicle
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine")
 * @ORM\Table(name="vehicle")
 */
class Vehicle extends CommonBaseEntity {
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
     * @ORM\Column(type="string", length=50)
     */
    protected $licenceNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="date")
     */
    protected $dateOfFirstRegistration;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $parking;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $managementDetails;

    /**
     * @ORM\OneToMany(targetEntity="ServicePlan", mappedBy="vehicle")
     * @ORM\JoinColumn(name="serviceplan_id", referencedColumnName="id")
     */
    protected $servicePlans;

    /**
     * @ORM\ManyToOne(targetEntity="Driver", inversedBy="supervisedVehicles")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id")
     */
    protected $supervisor;

    /**
     * @ORM\ManyToOne(targetEntity="VehicleCategory", inversedBy="vehicles")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="VehicleDepot", inversedBy="vehicles")
     * @ORM\JoinColumn(name="depot_id", referencedColumnName="id")
     */
    protected $depot;

    protected function __construct() {
        $this->servicePlans = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $name
     * @param $licenceNumber
     * @param $dateOfFirstRegistration
     * @param $parkingLotNumber
     * @param VehicleCategory $category
     * @param null $memo
     * @param null $managementDetails
     * @return Vehicle
     */
    public static function registerVehicle($name, $licenceNumber, $dateOfFirstRegistration,
                                           $parkingLotNumber, VehicleCategory $category,
                                           $memo = null, $managementDetails = null) {
        $vehicle = new Vehicle();
        $vehicle->setName($name);
        $vehicle->setLicenceNumber($licenceNumber);
        $vehicle->setDateOfFirstRegistration($dateOfFirstRegistration);
        $vehicle->setParking($parkingLotNumber);
        $vehicle->setCategory($category);
        $vehicle->setMemo($memo);
        $vehicle->setManagementDetails($managementDetails);
        $vehicle->activate();
        return $vehicle;
    }

    /**
     * @param null $name
     * @param null $licenceNumber
     * @param null $dateOfFirstRegistration
     * @param null $parkingLotNumber
     * @param VehicleCategory $category
     * @param null $memo
     * @param null $managementDetails
     */
    public function updateVehicleData($name = null, $licenceNumber = null,
                                      $dateOfFirstRegistration = null, $parkingLotNumber = null,
                                      VehicleCategory $category = null, $memo = null, $managementDetails = null) {
        if (!empty($name)) {
            $this->name = $name;
        }
        if (!empty($licenceNumber)) {
            $this->licenceNumber = $licenceNumber;
        }
        if (!empty($dateOfFirstRegistration)) {
            $this->dateOfFirstRegistration = $dateOfFirstRegistration;
        }
        $this->parking = $parkingLotNumber;
        if (!empty($category)) {
            $this->category = $category;
        }
        $this->memo = $memo;
        $this->managementDetails = $managementDetails;
        $this->updateModifiedDate();
    }

    public static function removeVehicle(Vehicle $vehicle) {
        foreach ($vehicle->getServicePlans() as $s) {
            /**@var $s ServicePlan */
            $s->removeVehicle();
        }
        $vehicle->removeSupervisor();
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param Driver $driver
     */
    public function assignSupervisor(Driver $driver) {
        $this->supervisor = $driver;
    }

    public function removeSupervisor() {
        $this->supervisor = null;
    }

    /**
     * @param VehicleDepot $vehicleDepot
     */
    public function assignDepot(VehicleDepot $vehicleDepot) {
        $this->depot = $vehicleDepot;
    }

    public function removeDepot() {
        $this->depot = null;
    }

    /**
     * @return Driver
     */
    public function getSupervisor() {
        return $this->supervisor;
    }

    /**
     * @param ServicePlan $servicePlan
     */
    public function assignServicePlan(ServicePlan $servicePlan) {
        $this->servicePlans->add($servicePlan);
        $servicePlan->assignVehicle($this);
    }

    /**
     * @param ServicePlan $servicePlan
     */
    public function removeServicePlan(ServicePlan $servicePlan) {
        $this->servicePlans->removeElement($servicePlan);
    }

    /**
     * @param VehicleCategory $category
     */
    public function setCategory(VehicleCategory $category) {
        $this->category = $category;
    }

    /**
     * @return VehicleCategory
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param mixed $dateOfFirstRegistration
     */
    public function setDateOfFirstRegistration($dateOfFirstRegistration) {
        $this->dateOfFirstRegistration = $dateOfFirstRegistration;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfFirstRegistration() {
        return $this->dateOfFirstRegistration;
    }

    /**
     * @param mixed $managementDetails
     */
    public function setManagementDetails($managementDetails) {
        $this->managementDetails = $managementDetails;
    }

    /**
     * @return mixed
     */
    public function getManagementDetails() {
        return $this->managementDetails;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * @param mixed $licenceNumber
     */
    public function setLicenceNumber($licenceNumber) {
        $this->licenceNumber = $licenceNumber;
    }

    /**
     * @return mixed
     */
    public function getLicenceNumber() {
        return $this->licenceNumber;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
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
     * @param mixed $parkingLotNumber
     */
    public function setParking($parkingLotNumber) {
        $this->parking = $parkingLotNumber;
    }

    /**
     * @return mixed
     */
    public function getParking() {
        return $this->parking;
    }

    /**
     * @return mixed
     */
    public function getServicePlans() {
        return $this->servicePlans;
    }

    /**
     * @return VehicleDepot
     */
    public function getDepot() {
        return $this->depot;
    }
}
