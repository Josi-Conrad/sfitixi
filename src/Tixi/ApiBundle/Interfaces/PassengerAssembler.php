<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 21:11
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\CoreDomain\Passenger;

class PassengerAssembler {

    public static function registerPassenger(Passenger $passenger, PassengerDTO $passengerDTO) {
        $passenger->registerPassenger(
            $passengerDTO->getTitle(),
            $passengerDTO->getFirstname(),
            $passengerDTO->getLastname(),
            $passengerDTO->getTelephone(),
            $passengerDTO->getIsInWheelChair()
        );

        return $passenger;
    }

    public static function toDTO(Passenger $passenger) {
        $passengerDTO = new PassengerDTO();
        $passengerDTO->setTitle($passenger->getTitle());
        $passengerDTO->setFirstname($passenger->getFirstname());
        $passengerDTO->setLastname($passenger->getLastname());
        $passengerDTO->setTelephone($passenger->getTelephone());
        $passengerDTO->setIsInWheelChair($passenger->getIsInWheelChair());

        return $passengerDTO;
    }
}