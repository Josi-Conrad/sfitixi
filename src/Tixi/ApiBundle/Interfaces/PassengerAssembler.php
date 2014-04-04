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
        $entryDate = $this->dateTimeService->convertLocalDateTimeToUTCDateTime($passengerDTO->entryDate);
        $birthday = $this->dateTimeService->convertLocalDateTimeToUTCDateTime($passengerDTO->birthday);
        $passenger = Passenger::registerPassenger($passengerDTO->title, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            Address::registerAddress(
                $passengerDTO->street, $passengerDTO->postalCode,
                $passengerDTO->city, $passengerDTO->country),
            $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $entryDate, $birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice
        );
        foreach($passengerDTO->handicaps as $handicap){
            $passenger->assignHandicap($handicap);
        }
        foreach($passengerDTO->insurances as $insurance){
            $passenger->assignInsurance($insurance);
        }
        return $passenger;
    }

    /**
     * @param Passenger $passenger
     * @param PassengerRegisterDTO $passengerDTO
     * @return Passenger
     */
    public function registerDTOToPassenger(Passenger $passenger, PassengerRegisterDTO $passengerDTO) {
        $entryDate = $this->dateTimeService->convertLocalDateTimeToUTCDateTime($passengerDTO->entryDate);
        $birthday = $this->dateTimeService->convertLocalDateTimeToUTCDateTime($passengerDTO->birthday);
        $address = $passenger->getAddress();

        $address->updateAddressBasicData($passengerDTO->street, $passengerDTO->postalCode,
            $passengerDTO->city, $passengerDTO->country);
        $passenger->updatePassengerBasicData($passengerDTO->title, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            $address, $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $entryDate, $birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice);

        $passenger->setHandicaps($passengerDTO->handicaps);
        $passenger->setInsurances($passengerDTO->insurances);

        return $passenger;
    }

    /**
     * @param Passenger $passenger
     * @return PassengerRegisterDTO
     */
    public function passengerToPassengerRegisterDTO(Passenger $passenger) {
        $passengerDTO = new PassengerRegisterDTO();
        $passengerDTO->person_id = $passenger->getId();
        $passengerDTO->isActive = $passenger->getIsActive();
        $passengerDTO->title = $passenger->getTitle();
        $passengerDTO->firstname = $passenger->getFirstname();
        $passengerDTO->lastname = $passenger->getLastname();
        $passengerDTO->telephone = $passenger->getTelephone();
        $passengerDTO->email = $passenger->getEmail();
        $passengerDTO->entryDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($passenger->getEntryDate());
        $passengerDTO->birthday = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($passenger->getBirthday());
        $passengerDTO->extraMinutes = $passenger->getExtraMinutes();
        $passengerDTO->details = $passenger->getDetails();

        $passengerDTO->isInWheelChair = $passenger->getIsInWheelChair();
        $passengerDTO->isOverweight = $passenger->getIsOverweight();
        $passengerDTO->gotMonthlyBilling = $passenger->getGotMonthlyBilling();
        $passengerDTO->notice = $passenger->getNotice();

        $passengerDTO->handicaps = $passenger->getHandicaps();
        $passengerDTO->insurances = $passenger->getInsurances();

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
            $dtoArray[] = $this->passengerToPassengerListDTO($passenger);
        }
        return $dtoArray;
    }

    /**
     * @param Passenger $passenger
     * @return PassengerListDTO
     */
    public function passengerToPassengerListDTO(Passenger $passenger) {
        $passengerListDTO = new PassengerListDTO();
        $passengerListDTO->id = $passenger->getId();
        $passengerListDTO->isActive = $passenger->getIsActive();
        $passengerListDTO->title = $passenger->getTitle();
        $passengerListDTO->firstname = $passenger->getFirstname();
        $passengerListDTO->telephone = $passenger->getTelephone();
        $passengerListDTO->lastname = $passenger->getLastname();
        $passengerListDTO->street = $passenger->getAddress()->getStreet();
        $passengerListDTO->city = $passenger->getAddress()->getCity();
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