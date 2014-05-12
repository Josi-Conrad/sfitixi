<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:02
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\POIKeyword;

/**
 * Class PoiKeywordAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class PoiKeywordAssembler {
    /**
     * @param PoiKeywordRegisterDTO $dto
     * @return POIKeyword
     */
    public function registerDTOtoNewPoiKeyword(PoiKeywordRegisterDTO $dto) {
        $poiKeyword = POIKeyword::registerPOIKeyword($dto->name, $dto->memo);
        return $poiKeyword;
    }

    /**
     * @param POIKeyword $poiKeyword
     * @param PoiKeywordRegisterDTO $dto
     */
    public function registerDTOtoPoiKeyword(POIKeyword $poiKeyword, PoiKeywordRegisterDTO $dto) {
        $poiKeyword->updateData($dto->name, $dto->memo);
    }

    /**
     * @param POIKeyword $poiKeyword
     * @return PoiKeywordRegisterDTO
     */
    public function toPoiKeywordRegisterDTO(POIKeyword $poiKeyword) {
        $poiKeywordDTO = new PoiKeywordRegisterDTO();
        $poiKeywordDTO->id = $poiKeyword->getId();
        $poiKeywordDTO->name = $poiKeyword->getName();
        $poiKeywordDTO->memo = $poiKeyword->getMemo();
        return $poiKeywordDTO;
    }

    /**
     * @param $poiKeywords
     * @return array
     */
    public function poiKeywordsToPoiKeywordListDTOs($poiKeywords) {
        $dtoArray = array();
        foreach($poiKeywords as $poiKeyword) {
            $dtoArray[] = $this->toPoiKeywordListDTO($poiKeyword);
        }
        return $dtoArray;
    }

    /**
     * @param POIKeyword $poiKeyword
     * @return VehicleCategoryListDTO
     */
    public function toPoiKeywordListDTO(POIKeyword $poiKeyword) {
        $poiKeywordListDTO = new VehicleCategoryListDTO();
        $poiKeywordListDTO->id = $poiKeyword->getId();
        $poiKeywordListDTO->name = $poiKeyword->getName();
        return $poiKeywordListDTO;
    }
} 