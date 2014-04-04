<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces;


use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Absent;

class AbsentAssembler {

    //injected by service container via setter method
    /** @var $dateTimeService  DateTimeService */
    private $dateTimeService;

    /**
     * @param AbsentRegisterDTO $absentDTO
     * @return Absent
     */
    public function registerDTOtoNewAbsent(AbsentRegisterDTO $absentDTO) {
        $absent = Absent::registerAbsent(
            $absentDTO->subject,
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($absentDTO->startDate),
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($absentDTO->endDate));
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
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($absentDTO->startDate),
            $this->dateTimeService->convertLocalDateTimeToUTCDateTime($absentDTO->endDate));
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
        $absentDTO->startDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($absent->getStartDate());
        $absentDTO->endDate = $this->dateTimeService->convertUTCDateTimeToLocalDateTime($absent->getEndDate());
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
        $absentEmbeddedListDTO->startDate = $this->dateTimeService->convertUTCDateTimeToLocalString($absent->getStartDate());
        $absentEmbeddedListDTO->endDate = $this->dateTimeService->convertUTCDateTimeToLocalString($absent->getEndDate());
        return $absentEmbeddedListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }

}