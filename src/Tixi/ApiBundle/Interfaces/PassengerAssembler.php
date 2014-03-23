<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.03.14
 * Time: 16:53
 */

namespace Tixi\ApiBundle\Interfaces;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Address;

class PassengerAssembler {
    /**
     * injected by service container via setter method
     * @var \Tixi\ApiBundle\Helper\DateTimeService
     */
    private $dateTimeService;


    /**
     * @param PassengerRegisterDTO $passengerDTO
     * @return Passenger
     */
    public function registerDTOtoNewPassenger(PassengerRegisterDTO $passengerDTO) {
        return Passenger::registerPassenger($passengerDTO->title, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            Address::registerAddress(
                $passengerDTO->street, $passengerDTO->postalCode,
                $passengerDTO->city, $passengerDTO->country),
            $passengerDTO->handicap, $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $passengerDTO->entryDate, $passengerDTO->birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice
        );
    }

    /**
     * @param Passenger $passenger
     * @param PassengerRegisterDTO $passengerDTO
     * @return Passenger
     */
    public function registerDTOToPassenger(Passenger $passenger, PassengerRegisterDTO $passengerDTO) {
        $passenger->updatePassengerBasicData($passengerDTO->title, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            $passenger->getAddress()->updateAddressBasicData(
                $passengerDTO->street, $passengerDTO->postalCode,
                $passengerDTO->city, $passengerDTO->country),
            $passengerDTO->handicap, $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $passengerDTO->entryDate, $passengerDTO->birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice
        );
        $passenger->setIsActive($passengerDTO->isActive);
        return $passenger;
    }

    /**
     * @param Passenger $passenger
     * @return PassengerRegisterDTO
     */
    public function toPassengerRegisterDTO(Passenger $passenger) {
        $passengerDTO = new PassengerRegisterDTO();
        $passengerDTO->id = $passenger->getId();
        $passengerDTO->isActive = $passenger->getIsActive();
        $passengerDTO->title = $passenger->getTitle();
        $passengerDTO->firstname = $passenger->getFirstname();
        $passengerDTO->lastname = $passenger->getLastname();
        $passengerDTO->telephone = $passenger->getTelephone();
        $passengerDTO->email = $passenger->getEmail();
        $passengerDTO->entryDate = $passenger->getEntryDate();
        $passengerDTO->birthday = $passenger->getBirthday();
        $passengerDTO->extraMinutes = $passenger->getExtraMinutes();
        $passengerDTO->details = $passenger->getDetails();

        $passengerDTO->isInWheelChair = $passenger->getIsInWheelChair();
        $passengerDTO->isOverweight = $passenger->getIsOverweight();
        $passengerDTO->gotMonthlyBilling = $passenger->getGotMonthlyBilling();
        $passengerDTO->notice = $passenger->getNotice();

        $passengerDTO->handicap = $passenger->getHandicap()->getName();

        $passengerDTO->street = $passenger->getAddress()->getStreet();
        $passengerDTO->postalCode = $passenger->getAddress()->getPostalCode();
        $passengerDTO->city = $passenger->getAddress()->getCity();
        $passengerDTO->country = $passenger->getAddress()->getCountry();

        return $passengerDTO;
    }


    /**
     * @param $passengers
     * @return array
     */
    public function passengersToPassengerListDTOs($passengers) {
        $dtoArray = array();
        foreach ($passengers as $passenger) {
            $dtoArray[] = $this->toPassengerListDTO($passenger);
        }
        return $dtoArray;
    }

    /**
     * @param Passenger $passenger
     * @return PassengerListDTO
     */
    public function toPassengerListDTO(Passenger $passenger) {
        $passengerListDTO = new PassengerListDTO();
        $passengerListDTO->id = $passenger->getId();
        $passengerListDTO->isActive = $passenger->getIsActive();
        $passengerListDTO->title = $passenger->getTitle();
        $passengerListDTO->firstname = $passenger->getFirstname();
        $passengerListDTO->telephone = $passenger->getTelephone();
        $passengerListDTO->lastname = $passenger->getLastname();
        $passengerListDTO->street = $passenger->getAddress()->getStreet();
        $passengerListDTO->city = $passenger->getAddress()->getCity();
        $passengerListDTO->handicap = $passenger->getHandicap()->getName();
        return $passengerListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }
}