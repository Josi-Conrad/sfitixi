<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:48
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Handicap;

/**
 * Class HandicapAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class HandicapAssembler {
    /**
     * @param HandicapRegisterDTO $dto
     * @return Handicap
     */
    public function registerDTOtoNewHandicap(HandicapRegisterDTO $dto) {
        $handicap = Handicap::registerHandicap($dto->name, $dto->memo);
        return $handicap;
    }

    /**
     * @param Handicap $handicap
     * @param HandicapRegisterDTO $dto
     */
    public function registerDTOtoHandicap(Handicap $handicap, HandicapRegisterDTO $dto) {
        $handicap->updateData($dto->name, $dto->memo);
    }

    /**
     * @param Handicap $handicap
     * @return HandicapRegisterDTO
     */
    public function toHandicapRegisterDTO(Handicap $handicap) {
        $handicapDTO = new HandicapRegisterDTO();
        $handicapDTO->id = $handicap->getId();
        $handicapDTO->name = $handicap->getName();
        $handicapDTO->memo = $handicap->getMemo();
        return $handicapDTO;
    }

    /**
     * @param $handicaps
     * @return array
     */
    public function handicapsToHandicapListDTOs($handicaps) {
        $dtoArray = array();
        foreach($handicaps as $handicap) {
            $dtoArray[] = $this->toHandicapListDTO($handicap);
        }
        return $dtoArray;
    }

    /**
     * @param Handicap $handicap
     * @return HandicapListDTO
     */
    public function toHandicapListDTO(Handicap $handicap) {
        $handicapListDTO = new HandicapListDTO();
        $handicapListDTO->id = $handicap->getId();
        $handicapListDTO->name = $handicap->getName();
        return $handicapListDTO;
    }
} 