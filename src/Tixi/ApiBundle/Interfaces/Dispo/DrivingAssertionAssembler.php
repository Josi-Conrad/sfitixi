<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.05.14
 * Time: 11:07
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Tixi\CoreDomain\Dispo\DrivingAssertion;

/**
 * Class DrivingAssertionAssembler
 * @package Tixi\ApiBundle\Interfaces\Dispo
 */
class DrivingAssertionAssembler {
    /**
     * @param $drivingAssertions
     * @return array
     */
    public function drivingAssertionToDrivngAssertionEmbeddedListDTOs($drivingAssertions) {
        $dtoArray = array();
        foreach ($drivingAssertions as $drivingAssertion) {
            $dtoArray[] = $this->drivingAssertionToDrivngAssertionEmbeddedListDTO($drivingAssertion);
        }
        return $dtoArray;
    }

    /**
     * @param DrivingAssertion $drivingAssertion
     * @return DrivingAssertionEmbeddedListDTO
     */
    public function drivingAssertionToDrivngAssertionEmbeddedListDTO(DrivingAssertion $drivingAssertion) {
        $dto = new DrivingAssertionEmbeddedListDTO();
        $dto->id = $drivingAssertion->getId();
        $dto->driverId = $drivingAssertion->getDriver()->getId();
        $dto->date = $drivingAssertion->getShift()->getWorkingDay()->getDateString();
        $dto->shift = $drivingAssertion->getShift()->getShiftType()->getName();
        return $dto;
    }

} 