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
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Address;

/**
 * Class PassengerAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class PassengerAssembler {

    /** @var  AddressAssembler $addressAssembler */
    protected $addressAssembler;

    /**
     * @param PassengerRegisterDTO $passengerDTO
     * @return Passenger
     */
    public function registerDTOtoNewPassenger(PassengerRegisterDTO $passengerDTO) {
        $passenger = Passenger::registerPassenger($passengerDTO->gender, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            $this->addressAssembler->addressLookaheadDTOtoAddress($passengerDTO->lookaheadaddress),
            $passengerDTO->title,
            $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $passengerDTO->entryDate, $passengerDTO->birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice,
            $passengerDTO->correspondenceAddress, $passengerDTO->billingAddress, $passengerDTO->isBillingAddress
        );
        foreach ($passengerDTO->handicaps as $handicap) {
            $passenger->assignHandicap($handicap);
        }
        foreach ($passengerDTO->insurances as $insurance) {
            $passenger->assignInsurance($insurance);
        }
        $passenger->assignPreferredVehicleCategory($passengerDTO->preferredVehicleCategory);
        return $passenger;
    }

    /**
     * @param Passenger $passenger
     * @param PassengerRegisterDTO $passengerDTO
     * @return Passenger
     */
    public function registerDTOToPassenger(Passenger $passenger, PassengerRegisterDTO $passengerDTO) {
        $passenger->updatePassengerData($passengerDTO->gender, $passengerDTO->firstname,
            $passengerDTO->lastname, $passengerDTO->telephone,
            $this->addressAssembler->addressLookaheadDTOtoAddress($passengerDTO->lookaheadaddress), $passengerDTO->title, $passengerDTO->isInWheelChair,
            $passengerDTO->gotMonthlyBilling, $passengerDTO->isOverweight,
            $passengerDTO->email, $passengerDTO->entryDate, $passengerDTO->birthday,
            $passengerDTO->extraMinutes, $passengerDTO->details, $passengerDTO->notice,
            $passengerDTO->correspondenceAddress, $passengerDTO->billingAddress, $passengerDTO->isBillingAddress);
        $passenger->setHandicaps($passengerDTO->handicaps);
        $passenger->setInsurances($passengerDTO->insurances);
        $passenger->assignPreferredVehicleCategory($passengerDTO->preferredVehicleCategory);
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
        $passengerDTO->gender = $passenger->getGender();
        $passengerDTO->title = $passenger->getTitle();
        $passengerDTO->firstname = $passenger->getFirstname();
        $passengerDTO->lastname = $passenger->getLastname();
        $passengerDTO->telephone = $passenger->getTelephone();
        $passengerDTO->email = $passenger->getEmail();
        $passengerDTO->entryDate = $passenger->getEntryDate();
        $passengerDTO->birthday = $passenger->getBirthday();
        $passengerDTO->extraMinutes = $passenger->getExtraMinutes();
        $passengerDTO->details = $passenger->getDetails();
        $passengerDTO->preferredVehicleCategory = $passenger->getPreferredVehicleCategory();

        $passengerDTO->isInWheelChair = $passenger->getIsInWheelChair();
        $passengerDTO->isOverweight = $passenger->getIsOverweight();
        $passengerDTO->gotMonthlyBilling = $passenger->getGotMonthlyBilling();
        $passengerDTO->notice = $passenger->getNotice();

        $passengerDTO->handicaps = $passenger->getHandicaps();
        $passengerDTO->insurances = $passenger->getInsurances();

        $passengerDTO->correspondenceAddress = $passenger->getCorrespondenceAddress();
        $passengerDTO->billingAddress = $passenger->getBillingAddress();
        $passengerDTO->isBillingAddress = $passenger->getIsBillingAddress();

        $passengerDTO->lookaheadaddress = $this->addressAssembler->addressToAddressLookaheadDTO($passenger->getAddress());

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
        $passengerListDTO->gender = $passenger->getGenderAsString();
        $passengerListDTO->firstname = $passenger->getFirstname();
        $passengerListDTO->telephone = $passenger->getTelephone();
        $passengerListDTO->lastname = $passenger->getLastname();
        $passengerListDTO->street = $passenger->getAddress()->getStreet();
        $passengerListDTO->city = $passenger->getAddress()->getCity();
        $passengerListDTO->isInWheelChair = $passenger->getIsInWheelChairAsString();
        $passengerListDTO->gotMonthlyBilling = $passenger->getMonthlyBillingAsString();
        $passengerListDTO->insurances = $passenger->getInsurancesAsString();
        return $passengerListDTO;
    }

    public function setAddressAssembler(AddressAssembler $assembler) {
        $this->addressAssembler = $assembler;
    }
}