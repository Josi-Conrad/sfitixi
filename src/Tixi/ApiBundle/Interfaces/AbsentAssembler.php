<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces;

use Tixi\CoreDomain\Absent;

class AbsentAssembler {
    /**
     * @param AbsentRegisterDTO $absentDTO
     * @return Absent
     */
    public function registerDTOtoNewAbsent(AbsentRegisterDTO $absentDTO) {
        $absent = Absent::registerAbsent(
            $absentDTO->subject,
            $absentDTO->startDate,
            $absentDTO->endDate);
        return $absent;
    }

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @param Absent $absent
     * @return Absent
     */
    public function registerDTOtoAbsent(AbsentRegisterDTO $absentDTO, Absent $absent) {
        $absent->updateBasicData(
            $absentDTO->subject,
            $absentDTO->startDate,
            $absentDTO->endDate);
        return $absent;
    }

    /**
     * @param Absent $absent
     * @return AbsentRegisterDTO
     */
    public function absentToAbsentRegisterDTO(Absent $absent) {
        $absentDTO = new AbsentRegisterDTO();
        $absentDTO->id = $absent->getId();
        $absentDTO->personId = $absent->getPerson()->getId();
        $absentDTO->subject = $absent->getSubject();
        $absentDTO->startDate = $this->convertDateToString($absent->getStartDate());
        $absentDTO->endDate = $this->convertDateToString($absent->getEndDate());
        return $absentDTO;
    }

    /**
     * @param $absents
     * @return array
     */
    public function absentsToAbsentEmbeddedListDTOs($absents) {
        $dtoArray = array();
        foreach ($absents as $absent) {
            $dtoArray[] = $this->absentsToAbsentEmbeddedListDTO($absent);
        }
        return $dtoArray;
    }

    /**
     * @param Absent $absent
     * @return AbsentEmbeddedListDTO
     */
    public function absentsToAbsentEmbeddedListDTO(Absent $absent) {
        $absentEmbeddedListDTO = new AbsentEmbeddedListDTO();
        $absentEmbeddedListDTO->id = $absent->getId();
        $absentEmbeddedListDTO->personId = $absent->getPerson()->getId();
        $absentEmbeddedListDTO->subject = $absent->getSubject();
        $absentEmbeddedListDTO->startDate = $this->convertDateToString($absent->getStartDate());
        $absentEmbeddedListDTO->endDate = $this->convertDateToString($absent->getEndDate());
        return $absentEmbeddedListDTO;
    }

    private function convertStringToDate($string){
        $date = \DateTime::createFromFormat('d.m.Y', $string);
        if ($date) {
            $newDate = clone $date;
            return $newDate;
        }
        return $date;
    }

    private function convertDateToString($date){
        $stringDate = clone $date;
        return $stringDate->format('d.m.Y');
    }
}