<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Interfaces;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tixi\App\Address\AddressManagement;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Address;

/**
 * Class DriverAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class DriverAssembler {

    /** @var  AddressAssembler $addressAssembler */
    protected $addressAssembler;

    /**
     * @param DriverRegisterDTO $driverDTO
     * @throws \Exception
     * @return Driver
     */
    public function registerDTOtoNewDriver(DriverRegisterDTO $driverDTO) {
        $driver = Driver::registerDriver($driverDTO->gender, $driverDTO->firstname,
            $driverDTO->lastname, $driverDTO->telephone,
            $this->addressAssembler->addressLookaheadDTOtoAddress($driverDTO->lookaheadaddress),
            $driverDTO->licenceNumber, $driverDTO->driverCategory, $driverDTO->wheelChairAttendance,
            $driverDTO->title, $driverDTO->email, $driverDTO->entryDate, $driverDTO->birthday,
            $driverDTO->extraMinutes, $driverDTO->details
        );
        return $driver;
    }

    /**
     * @param Driver $driver
     * @param DriverRegisterDTO $driverDTO
     * @throws \Exception
     * @return Driver
     */
    public function registerDTOToDriver(DriverRegisterDTO $driverDTO, Driver $driver) {
        $driver->updateDriverData($driverDTO->gender, $driverDTO->firstname,
            $driverDTO->lastname, $driverDTO->telephone,
            $this->addressAssembler->addressLookaheadDTOtoAddress($driverDTO->lookaheadaddress), $driverDTO->licenceNumber, $driverDTO->driverCategory,
            $driverDTO->wheelChairAttendance, $driverDTO->title,
            $driverDTO->email, $driverDTO->entryDate, $driverDTO->birthday,
            $driverDTO->extraMinutes, $driverDTO->details);
        return $driver;
    }

    /**
     * @param Driver $driver
     * @return DriverRegisterDTO
     */
    public function driverToDriverRegisterDTO(Driver $driver) {
        $driverDTO = new DriverRegisterDTO();
        $driverDTO->person_id = $driver->getId();
        $driverDTO->isActive = $driver->getIsActive();
        $driverDTO->gender = $driver->getGender();
        $driverDTO->title = $driver->getTitle();
        $driverDTO->firstname = $driver->getFirstname();
        $driverDTO->lastname = $driver->getLastname();
        $driverDTO->telephone = $driver->getTelephone();
        $driverDTO->email = $driver->getEmail();
        $driverDTO->entryDate = $driver->getEntryDate();
        $driverDTO->birthday = $driver->getBirthday();
        $driverDTO->extraMinutes = $driver->getExtraMinutes();
        $driverDTO->details = $driver->getDetails();

        $driverDTO->licenceNumber = $driver->getLicenceNumber();
        $driverDTO->wheelChairAttendance = $driver->getWheelChairAttendance();

        $driverDTO->driverCategory = $driver->getDriverCategory()->getName();

        $driverDTO->lookaheadaddress = $this->addressAssembler->addressToAddressLookaheadDTO($driver->getAddress());

        return $driverDTO;
    }


    /**
     * @param $drivers
     * @return array
     */
    public function driversToDriverListDTOs($drivers) {
        $dtoArray = array();
        foreach ($drivers as $driver) {
            $dtoArray[] = $this->driverToDriverListDTO($driver);
        }
        return $dtoArray;
    }

    /**
     * @param Driver $driver
     * @return DriverListDTO
     */
    public function driverToDriverListDTO(Driver $driver) {
        $driverListDTO = new DriverListDTO();
        $driverListDTO->id = $driver->getId();
        $driverListDTO->isActive = $driver->getIsActive();
        $driverListDTO->gender = $driver->getGenderAsString();
        $driverListDTO->firstname = $driver->getFirstname();
        $driverListDTO->telephone = $driver->getTelephone();
        $driverListDTO->lastname = $driver->getLastname();
        $driverListDTO->street = $driver->getAddress()->getStreet();
        $driverListDTO->city = $driver->getAddress()->getCity();
        $driverListDTO->driverCategory = $driver->getDriverCategory()->getName();
        $driverListDTO->wheelChairAttendance = $driver->getWheelChairAttendanceAsString();
        return $driverListDTO;
    }

    public function setAddressAssembler(AddressAssembler $assembler) {
        $this->addressAssembler = $assembler;
    }


}