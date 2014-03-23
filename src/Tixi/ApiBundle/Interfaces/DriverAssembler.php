<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.03.14
 * Time: 16:53
 */

namespace Tixi\ApiBundle\Interfaces;


use Proxies\__CG__\Tixi\CoreDomain\Address;
use Proxies\__CG__\Tixi\CoreDomain\DriverCategory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\City;
use Tixi\CoreDomain\Country;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\PostalCode;
use Tixi\CoreDomain\Vehicle;

class DriverAssembler {
    /**
     * injected by service container via setter method
     * @var \Tixi\ApiBundle\Helper\DateTimeService
     */
    private $dateTimeService;


    /**
     * @param DriverRegisterDTO $driverDTO
     * @return Driver
     */
    public function registerDTOtoNewDriver(DriverRegisterDTO $driverDTO) {
        return Driver::registerDriver($driverDTO->title, $driverDTO->firstname,
            $driverDTO->lastname, $driverDTO->telephone, $driverDTO->licenseNumber,
            Address::registerAddress(
                $driverDTO->street, $driverDTO->postalCode,
                $driverDTO->city, $driverDTO->country),
            $driverDTO->driverCategory, $driverDTO->wheelChairAttendance,
            $driverDTO->email, $driverDTO->entryDate, $driverDTO->birthday,
            $driverDTO->extraMinutes, $driverDTO->details
        );
    }

    /**
     * @param Driver $driver
     * @param DriverRegisterDTO $driverDTO
     * @return Driver
     */
    public function registerDTOToDriver(Driver $driver, DriverRegisterDTO $driverDTO) {
        $driver->updateDriverBasicData($driverDTO->title, $driverDTO->firstname,
            $driverDTO->lastname, $driverDTO->telephone, $driverDTO->licenseNumber,
            $driver->getAddress()->updateAddressBasicData(
                $driverDTO->street, $driverDTO->postalCode,
                $driverDTO->city, $driverDTO->country),
            $driverDTO->driverCategory,
            $driverDTO->wheelChairAttendance, $driverDTO->email,
            $driverDTO->entryDate, $driverDTO->birthday,
            $driverDTO->extraMinutes, $driverDTO->details);
        $driver->setIsActive($driverDTO->isActive);
        return $driver;
    }

    /**
     * @param Driver $driver
     * @return DriverRegisterDTO
     */
    public function toDriverRegisterDTO(Driver $driver) {
        $driverDTO = new DriverRegisterDTO();
        $driverDTO->id = $driver->getId();
        $driverDTO->isActive = $driver->getIsActive();
        $driverDTO->title = $driver->getTitle();
        $driverDTO->firstname = $driver->getFirstname();
        $driverDTO->lastname = $driver->getLastname();
        $driverDTO->telephone = $driver->getTelephone();
        $driverDTO->email = $driver->getEmail();
        $driverDTO->entryDate = $driver->getEntryDate();
        $driverDTO->birthday = $driver->getBirthday();
        $driverDTO->extraMinutes = $driver->getExtraMinutes();
        $driverDTO->details = $driver->getDetails();

        $driverDTO->licenseNumber = $driver->getLicenceNumber();
        $driverDTO->wheelChairAttendance = $driver->getWheelChairAttendance();

        $driverDTO->driverCategory = $driver->getDriverCategory()->getName();

        $driverDTO->addressName = $driver->getAddress()->getName();
        $driverDTO->street = $driver->getAddress()->getStreet();
        $driverDTO->postalCode = $driver->getAddress()->getPostalCode();
        $driverDTO->city = $driver->getAddress()->getCity();
        $driverDTO->country = $driver->getAddress()->getCountry();

        return $driverDTO;
    }


    /**
     * @param $drivers
     * @return array
     */
    public function driversToDriverListDTOs($drivers) {
        $dtoArray = array();
        foreach ($drivers as $driver) {
            $dtoArray[] = $this->toDriverListDTO($driver);
        }
        return $dtoArray;
    }

    /**
     * @param Driver $driver
     * @return DriverListDTO
     */
    public function toDriverListDTO(Driver $driver) {
        $driverListDTO = new DriverListDTO();
        $driverListDTO->id = $driver->getId();
        $driverListDTO->isActive = $driver->getIsActive();
        $driverListDTO->title = $driver->getTitle();
        $driverListDTO->firstname = $driver->getFirstname();
        $driverListDTO->telephone = $driver->getTelephone();
        $driverListDTO->lastname = $driver->getLastname();
        $driverListDTO->street = $driver->getAddress()->getStreet();
        $driverListDTO->city = $driver->getAddress()->getCity();
        $driverListDTO->driverCategory = $driver->getDriverCategory()->getName();
        return $driverListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }
}