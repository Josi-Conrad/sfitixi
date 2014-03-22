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
use Tixi\ApiBundle\Form\VehicleType;
use Tixi\CoreDomain\Shared\Entity;

/**
 * Tixi\CoreDomain\Vehicle
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\VehicleRepositoryDoctrine")
 * @ORM\Table(name="vehicle")
 */
class Vehicle implements Entity{

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
     * @ORM\Column(type="integer")
     */
    protected $parkingLotNumber;

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
     * @ORM\ManyToOne(targetEntity="VehicleCategory")
     */
    protected $category;


    private function __construct() {
        $this->servicePlans = new ArrayCollection();
    }

    /**
     * @param $name
     * @param $licenceNumber
     * @param $dateOfFirstRegistration
     * @param $parkingLotNumber
     * @param VehicleCategory $category
     * @return Vehicle
     */
    public static function registerVehicle($name, $licenceNumber, $dateOfFirstRegistration, $parkingLotNumber, VehicleCategory $category) {
        $vehicle = new Vehicle();
        $vehicle->setName($name);
        $vehicle->setLicenceNumber($licenceNumber);
        $vehicle->setDateOfFirstRegistration($dateOfFirstRegistration);
        $vehicle->setParkingLotNumber($parkingLotNumber);
        $vehicle->setCategory($category);
        $vehicle->activate();
        return $vehicle;
    }

    /**
     * @param null $name
     * @param null $licenceNumber
     * @param null $dateOfFirstRegistration
     * @param null $parkingLotNumber
     * @param VehicleCategory $category
     */
    public function updateBasicData($name=null, $licenceNumber=null, $dateOfFirstRegistration=null, $parkingLotNumber=null, VehicleCategory $category=null) {
        if(!is_null($name)) {$this->name=$name;}
        if(!is_null($licenceNumber)) {$this->licenceNumber=$licenceNumber;}
        if(!is_null($dateOfFirstRegistration)) {$this->dateOfFirstRegistration=$dateOfFirstRegistration;}
        if(!is_null($parkingLotNumber)) {$this->parkingLotNumber=$parkingLotNumber;}
        if(!is_null($category)) {$this->category=$category;}
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    public function assignSupervisor(Driver $driver) {
        $this->supervisor = $driver;
    }

    /**
     * @param Driver $driver
     */
    public function removeSupervisor(Driver $driver) {
        $driver->removeSupervisedVehicle($this);
    }

    /**
     * @param ServicePlan $servicePlan
     */
    public function assignServicePlan(ServicePlan $servicePlan){
        $this->servicePlans->add($servicePlan);
        $servicePlan->setVehicle($this);
    }

    /**
     * @param ServicePlan $servicePlan
     */
    public function removeServicePlan(ServicePlan $servicePlan){
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
    public function setDateOfFirstRegistration($dateOfFirstRegistration)
    {
        $this->dateOfFirstRegistration = $dateOfFirstRegistration;
    }

    /**
     * @return mixed
     */
    public function getDateOfFirstRegistration()
    {
        return $this->dateOfFirstRegistration;
    }

    /**
     * @param mixed $managementDetails
     */
    public function setManagementDetails($managementDetails)
    {
        $this->managementDetails = $managementDetails;
    }

    /**
     * @return mixed
     */
    public function getManagementDetails()
    {
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
    public function setParkingLotNumber($parkingLotNumber) {
        $this->parkingLotNumber = $parkingLotNumber;
    }

    /**
     * @return mixed
     */
    public function getParkingLotNumber() {
        return $this->parkingLotNumber;
    }

    /**
     * @param mixed $servicePlans
     */
    public function setServicePlans($servicePlans) {
        $this->servicePlans = $servicePlans;
    }

    /**
     * @return mixed
     */
    public function getAssociatedServicePlansAsArrayCollection() {
        return $this->servicePlans;
    }


}
