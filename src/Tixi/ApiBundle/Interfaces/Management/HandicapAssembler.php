<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:48
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Handicap;

class HandicapAssembler {
    public function registerDTOtoNewHandicap(HandicapRegisterDTO $dto) {
        $poiKeyword = Handicap::registerHandicap($dto->name);
        return $poiKeyword;
    }

    public function registerDTOtoHandicap(Handicap $handicap, HandicapRegisterDTO $dto) {
        $handicap->updateData($dto->name);
    }

    public function toHandicapRegisterDTO(Handicap $handicap) {
        $poiKeywordDTO = new HandicapRegisterDTO();
        $poiKeywordDTO->id = $handicap->getId();
        $poiKeywordDTO->name = $handicap->getName();
        return $poiKeywordDTO;
    }

    public function poiKeywordsToHandicapListDTOs($handicaps) {
        $dtoArray = array();
        foreach($handicaps as $handicap) {
            $dtoArray[] = $this->toHandicapListDTO($handicap);
        }
        return $dtoArray;
    }

    public function toHandicapListDTO(Handicap $handicap) {
        $handicapListDTO = new HandicapListDTO();
        $handicapListDTO->id = $handicap->getId();
        $handicapListDTO->name = $handicap->getName();
        return $handicapListDTO;
    }
} 