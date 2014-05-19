<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Dispo\ShiftType;

/**
 * Class ShiftTypeAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class ShiftTypeAssembler {
    /**
     * injected by service container via setter method
     * @var $dateTimeService DateTimeService
     */
    private $dateTimeService;

    /**
     * @param ShiftTypeRegisterDTO $shiftTypeDTO
     * @return ShiftType
     */
    public function registerDTOtoNewShiftType(ShiftTypeRegisterDTO $shiftTypeDTO) {
        $shiftType = ShiftType::registerShiftType(
            $shiftTypeDTO->name,
            $shiftTypeDTO->start,
            $shiftTypeDTO->end,
            $shiftTypeDTO->memo);
        return $shiftType;
    }

    /**
     * @param ShiftTypeRegisterDTO $shiftTypeDTO
     * @param ShiftType $shiftType
     * @return ShiftType
     */
    public function registerDTOtoShiftType(ShiftTypeRegisterDTO $shiftTypeDTO, ShiftType $shiftType) {
        $shiftType->updateShiftTypeData(
            $shiftTypeDTO->name,
            $shiftTypeDTO->start,
            $shiftTypeDTO->end,
            $shiftTypeDTO->memo);
        return $shiftType;
    }

    /**
     * @param ShiftType $shiftType
     * @return ShiftTypeRegisterDTO
     */
    public function shiftTypeToShiftTypeRegisterDTO(ShiftType $shiftType) {
        $shiftTypeDTO = new ShiftTypeRegisterDTO();
        $shiftTypeDTO->id = $shiftType->getId();
        $shiftTypeDTO->name = $shiftType->getName();
        $shiftTypeDTO->start =
            $this->dateTimeService->convertToLocalDateTime($shiftType->getStart());
        $shiftTypeDTO->end =
            $this->dateTimeService->convertToLocalDateTime($shiftType->getEnd());
        $shiftTypeDTO->memo = $shiftType->getMemo();
        return $shiftTypeDTO;
    }

    /**
     * @param $shiftTypes
     * @return array
     */
    public function shiftTypesToShiftTypeListDTOs($shiftTypes) {
        $dtoArray = array();
        foreach ($shiftTypes as $shiftType) {
            $dtoArray[] = $this->shiftTypesToShiftTypeListDTO($shiftType);
        }
        return $dtoArray;
    }

    /**
     * @param ShiftType $shiftType
     * @return ShiftTypeListDTO
     */
    public function shiftTypesToShiftTypeListDTO(ShiftType $shiftType) {
        $shiftTypeEmbeddedListDTO = new ShiftTypeListDTO();
        $shiftTypeEmbeddedListDTO->id = $shiftType->getId();
        $shiftTypeEmbeddedListDTO->name = $shiftType->getName();
        $shiftTypeEmbeddedListDTO->start =
            $this->dateTimeService->convertToLocalTimeString($shiftType->getStart());
        $shiftTypeEmbeddedListDTO->end =
            $this->dateTimeService->convertToLocalTimeString($shiftType->getEnd());
        return $shiftTypeEmbeddedListDTO;
    }

    /**
     * @param $dateTimeService
     * Injected by service container
     */
    public function setDateTimeService(DateTimeService $dateTimeService) {
        $this->dateTimeService = $dateTimeService;
    }
}