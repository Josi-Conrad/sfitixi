<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:02
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\POIKeyword;

class PoiKeywordAssembler {
    public function registerDTOtoNewPoiKeyword(PoiKeywordRegisterDTO $dto) {
        $poiKeyword = POIKeyword::registerPOIKeyword($dto->name);
        return $poiKeyword;
    }

    public function registerDTOtoPoiKeyword(POIKeyword $poiKeyword, PoiKeywordRegisterDTO $dto) {
        $poiKeyword->updateData($dto->name);
    }

    public function toPoiKeywordRegisterDTO(POIKeyword $poiKeyword) {
        $poiKeywordDTO = new PoiKeywordRegisterDTO();
        $poiKeywordDTO->id = $poiKeyword->getId();
        $poiKeywordDTO->name = $poiKeyword->getName();
        return $poiKeywordDTO;
    }

    public function poiKeywordsToPoiKeywordListDTOs($poiKeywords) {
        $dtoArray = array();
        foreach($poiKeywords as $poiKeyword) {
            $dtoArray[] = $this->toPoiKeywordListDTO($poiKeyword);
        }
        return $dtoArray;
    }

    public function toPoiKeywordListDTO(POIKeyword $poiKeyword) {
        $poiKeywordListDTO = new VehicleCategoryListDTO();
        $poiKeywordListDTO->id = $poiKeyword->getId();
        $poiKeywordListDTO->name = $poiKeyword->getName();
        return $poiKeywordListDTO;
    }
} 