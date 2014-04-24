<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\CoreDomain\DriverCategory;

/**
 * Class DriverCategoryAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class DriverCategoryAssembler {

    /**
     * @param DriverCategoryRegisterDTO $driverCategoryDTO
     * @return DriverCategory
     */
    public function registerDTOtoNewDriverCategory(DriverCategoryRegisterDTO $driverCategoryDTO) {
        $driverCategory = DriverCategory::registerDriverCategory(
            $driverCategoryDTO->name);
        return $driverCategory;
    }

    /**
     * @param DriverCategoryRegisterDTO $driverCategoryDTO
     * @param DriverCategory $driverCategory
     * @return DriverCategory
     */
    public function registerDTOtoDriverCategory(DriverCategoryRegisterDTO $driverCategoryDTO, DriverCategory $driverCategory) {
        $driverCategory->updateDriverCategoryData(
            $driverCategoryDTO->name);
        return $driverCategory;
    }

    /**
     * @param DriverCategory $driverCategory
     * @return DriverCategoryRegisterDTO
     */
    public function driverCategoryToDriverCategoryRegisterDTO(DriverCategory $driverCategory) {
        $driverCategoryDTO = new DriverCategoryRegisterDTO();
        $driverCategoryDTO->id = $driverCategory->getId();
        $driverCategoryDTO->name = $driverCategory->getName();
        return $driverCategoryDTO;
    }

    /**
     * @param $driverCategorys
     * @return array
     */
    public function driverCategorysToDriverCategoryListDTOs($driverCategorys) {
        $dtoArray = array();
        foreach ($driverCategorys as $driverCategory) {
            $dtoArray[] = $this->driverCategorysToDriverCategoryListDTO($driverCategory);
        }
        return $dtoArray;
    }

    /**
     * @param DriverCategory $driverCategory
     * @return DriverCategoryEmbeddedListDTO
     */
    public function driverCategorysToDriverCategoryListDTO(DriverCategory $driverCategory) {
        $driverCategoryEmbeddedListDTO = new DriverCategoryListDTO();
        $driverCategoryEmbeddedListDTO->id = $driverCategory->getId();
        $driverCategoryEmbeddedListDTO->name = $driverCategory->getName();
        return $driverCategoryEmbeddedListDTO;
    }

}