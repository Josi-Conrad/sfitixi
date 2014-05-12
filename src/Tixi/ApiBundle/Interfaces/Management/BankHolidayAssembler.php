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
            $bankHolidayDTO->date,
            $bankHolidayDTO->memo);
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
            $bankHolidayDTO->date,
            $bankHolidayDTO->memo);
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
        $bankHolidayDTO->date = $bankHoliday->getDate();
        $bankHolidayDTO->memo = $bankHoliday->getMemo();
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
     * @return BankHolidayListDTO
     */
    public function bankHolidaysToBankHolidayListDTO(BankHoliday $bankHoliday) {
        $bankHolidayEmbeddedListDTO = new BankHolidayListDTO();
        $bankHolidayEmbeddedListDTO->id = $bankHoliday->getId();
        $bankHolidayEmbeddedListDTO->name = $bankHoliday->getName();
        $bankHolidayEmbeddedListDTO->date = $bankHoliday->getDate()->format('d.m.Y');
        return $bankHolidayEmbeddedListDTO;
    }

}