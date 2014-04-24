<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan;
use Tixi\CoreDomain\Dispo\Shift;

/**
 * Tixi\CoreDomain\Driver
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\DriverRepositoryDoctrine")
 * @ORM\Table(name="driver")
 */
class Driver extends Person {
    /**
     * @ORM\ManyToOne(targetEntity="DriverCategory")
     * @ORM\JoinColumn(name="driver_category", referencedColumnName="id")
     */
    protected $driverCategory;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="supervisor")
     * @ORM\JoinColumn(name="supervised_vehicle_id", referencedColumnName="id")
     */
    protected $supervisedVehicles;

    /**
     * @ORM\Column(type="string", length=45)
     */
    protected $licenceNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $wheelChairAttendance;

    /**
     * @ORM\OneToMany(targetEntity="Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionPlan", mappedBy="driver")
     */
    protected $repeatedDrivingAssertionPlans;

    protected function __construct($gender, $firstname, $lastname, $telephone, $address, $title = null,
                                   $email = null, $entryDate = null, $birthday = null,
                                   $extraMinutes = null, $details = null) {

        $this->supervisedVehicles = new ArrayCollection();
        $this->repeatedDrivingAssertionPlans = new ArrayCollection();
        parent::__construct($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details);
    }

    /**
     * @param $gender
     * @param $firstname
     * @param $lastname
     * @param $telephone
     * @param Address $address
     * @param $title
     * @param $licenceNumber
     * @param DriverCategory $driverCategory
     * @param bool $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     * @return Driver
     */
    public static function registerDriver($gender, $firstname, $lastname, $telephone, Address $address, $licenceNumber,
                                          DriverCategory $driverCategory, $wheelChairAttendance = true, $title = null, $email = null,
                                          $entryDate = null, $birthday = null, $extraMinutes = null, $details = null) {

        $driver = new Driver($gender, $firstname, $lastname, $telephone, $address, $title,
            $email, $entryDate, $birthday, $extraMinutes, $details);

        $driver->setLicenceNumber($licenceNumber);
        $driver->setDriverCategory($driverCategory);
        $driver->setWheelChairAttendance($wheelChairAttendance);

        return $driver;
    }

    /**
     * @param null $gender
     * @param null $firstname
     * @param null $lastname
     * @param null $telephone
     * @param Address $address
     * @param null $title
     * @param null $licenceNumber
     * @param DriverCategory $driverCategory
     * @param null $wheelChairAttendance
     * @param null $email
     * @param null $entryDate
     * @param null $birthday
     * @param null $extraMinutes
     * @param null $details
     */
    public function updateDriverData($gender = null, $firstname = null, $lastname = null, $telephone = null,
                                     Address $address = null, $licenceNumber = null, DriverCategory $driverCategory = null,
                                     $wheelChairAttendance = null, $title = null, $email = null, $entryDate = null, $birthday = null,
                                     $extraMinutes = null, $details = null) {

        parent::updatePersonData(
            $gender, $firstname, $lastname, $telephone, $address, $title, $email, $entryDate, $birthday, $extraMinutes, $details
        );

        if (!empty($licenceNumber)) {
            $this->setLicenceNumber($licenceNumber);
        }
        if (!empty($driverCategory)) {
            $this->setDriverCategory($driverCategory);
        }
        $this->setWheelChairAttendance($wheelChairAttendance);
    }

    /**
     * Remove Driver, delete all Associations
     * @param Driver $driver
     */
    public static function removeDriver(Driver $driver) {
        foreach ($driver->getSupervisedVehicles() as $v) {
            $driver->removeSupervisedVehicle($v);
        }
        $driver->removePerson();
    }

    public function isAvailableOn(Shift $shift) {

    }

    /**
     * @param Vehicle $vehicle
     */
    public function assignSupervisedVehicle(Vehicle $vehicle) {
        $this->supervisedVehicles->add($vehicle);
        $vehicle->assignSupervisor($this);
    }

    /**
     * @param Vehicle $vehicle
     */
    public function removeSupervisedVehicle(Vehicle $vehicle) {
        $vehicle->removeSupervisor();
        $this->supervisedVehicles->removeElement($vehicle);
    }

    public function assignRepeatedDrivingAssertionPlan(RepeatedDrivingAssertionPlan $assertionPlan) {
        $this->repeatedDrivingAssertionPlans->add($assertionPlan);
        $assertionPlan->assignDriver($this);
    }

    public function removeRepeatedDrivingAssertionPlan(RepeatedDrivingAssertionPlan $assertionPlan) {
        $assertionPlan->removeDriver();
        $this->repeatedDrivingAssertionPlans->removeElement($assertionPlan);
    }

    /**
     * @return Vehicle[]
     */
    public function getSupervisedVehicles() {
        return $this->supervisedVehicles;
    }


    /**
     * @param mixed $driverCategory
     */
    public function setDriverCategory($driverCategory) {
        $this->driverCategory = $driverCategory;
    }

    /**
     * @return DriverCategory
     */
    public function getDriverCategory() {
        return $this->driverCategory;
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
     * @param mixed $operationWish
     */
    public function setOperationWish($operationWish) {
        $this->operationWish = $operationWish;
    }

    /**
     * @return mixed
     */
    public function getOperationWish() {
        return $this->operationWish;
    }

    /**
     * @param mixed $wheelChairAttendance
     */
    public function setWheelChairAttendance($wheelChairAttendance) {
        $this->wheelChairAttendance = $wheelChairAttendance;
    }

    /**
     * @return mixed
     */
    public function getWheelChairAttendance() {
        return $this->wheelChairAttendance;
    }

    /**
     * @return mixed
     */
    public function getRepeatedDrivingAssertionPlans() {
        return $this->repeatedDrivingAssertionPlans;
    }
}
