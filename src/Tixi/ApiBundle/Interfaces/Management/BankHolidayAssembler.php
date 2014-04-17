<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\CoreDomain\BankHoliday;

/**
 * Class BankHolidayAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class BankHolidayAssembler {

    /**
     * @param BankHolidayRegisterDTO $bankHolidayDTO
     * @return BankHoliday
     */
    public function registerDTOtoNewBankHoliday(BankHolidayRegisterDTO $bankHolidayDTO) {
        $bankHoliday = BankHoliday::registerBankHoliday(
            $bankHolidayDTO->name,
            $bankHolidayDTO->startDate,
            $bankHolidayDTO->endDate);
        return $bankHoliday;
    }

    /**
     * @param BankHolidayRegisterDTO $bankHolidayDTO
     * @param BankHoliday $bankHoliday
     * @return BankHoliday
     */
    public function registerDTOtoBankHoliday(BankHolidayRegisterDTO $bankHolidayDTO, BankHoliday $bankHoliday) {
        $bankHoliday->updateBankHolidayData(
            $bankHolidayDTO->name,
            $bankHolidayDTO->startDate,
            $bankHolidayDTO->endDate);
        return $bankHoliday;
    }

    /**
     * @param BankHoliday $bankHoliday
     * @return BankHolidayRegisterDTO
     */
    public function bankHolidayToBankHolidayRegisterDTO(BankHoliday $bankHoliday) {
        $bankHolidayDTO = new BankHolidayRegisterDTO();
        $bankHolidayDTO->id = $bankHoliday->getId();
        $bankHolidayDTO->name = $bankHoliday->getName();
        $bankHolidayDTO->startDate = $bankHoliday->getStartDate();
        $bankHolidayDTO->endDate = $bankHoliday->getEndDate();
        return $bankHolidayDTO;
    }

    /**
     * @param $bankHolidays
     * @return array
     */
    public function bankHolidaysToBankHolidayListDTOs($bankHolidays) {
        $dtoArray = array();
        foreach ($bankHolidays as $bankHoliday) {
            $dtoArray[] = $this->bankHolidaysToBankHolidayListDTO($bankHoliday);
        }
        return $dtoArray;
    }

    /**
     * @param BankHoliday $bankHoliday
     * @return BankHolidayEmbeddedListDTO
     */
    public function bankHolidaysToBankHolidayListDTO(BankHoliday $bankHoliday) {
        $bankHolidayEmbeddedListDTO = new BankHolidayListDTO();
        $bankHolidayEmbeddedListDTO->id = $bankHoliday->getId();
        $bankHolidayEmbeddedListDTO->name = $bankHoliday->getName();
        $bankHolidayEmbeddedListDTO->startDate = $bankHoliday->getStartDate()->format('d.m.Y');
        $bankHolidayEmbeddedListDTO->endDate = $bankHoliday->getEndDate()->format('d.m.Y');
        return $bankHolidayEmbeddedListDTO;
    }

}