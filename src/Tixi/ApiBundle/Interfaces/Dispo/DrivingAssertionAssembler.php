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
            //manually sort list because fields are computed
            usort($dtoArray, function(DrivingAssertionEmbeddedListDTO $a, DrivingAssertionEmbeddedListDTO $b) {
                if($a->dateAsDateTime == $b->dateAsDateTime) return 0;
                return ($a->dateAsDateTime < $b->dateAsDateTime ? -1 : 1);
            });
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
        $dto->dateAsDateTime = $drivingAssertion->getShift()->getWorkingDay();
        $dto->shift = $drivingAssertion->getShift()->getShiftType()->getName();
        return $dto;
    }

} 