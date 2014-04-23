<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 14:08
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\CoreDomain\VehicleCategory;

/**
 * Class VehicleCategoryAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class VehicleCategoryAssembler {
    /**
     * @param VehicleCategoryRegisterDTO $dto
     * @return VehicleCategory
     */
    public function registerDTOtoNewVehicleCategory(VehicleCategoryRegisterDTO $dto) {
        $vehicleCategory = VehicleCategory::registerVehicleCategory($dto->name, $dto->amountOfSeats, $dto->amountOfWheelChairs);
        return $vehicleCategory;
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @param VehicleCategoryRegisterDTO $dto
     */
    public function registerDTOtoVehicleCategory(VehicleCategory $vehicleCategory, VehicleCategoryRegisterDTO $dto) {
        $vehicleCategory->updateData($dto->name, $dto->amountOfSeats, $dto->amountOfWheelChairs);
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return VehicleCategoryRegisterDTO
     */
    public function toVehicleCategoryRegisterDTO(VehicleCategory $vehicleCategory) {
        $vehicleCategoryDTO = new VehicleCategoryRegisterDTO();
        $vehicleCategoryDTO->id = $vehicleCategory->getId();
        $vehicleCategoryDTO->name = $vehicleCategory->getName();
        $vehicleCategoryDTO->amountOfSeats = $vehicleCategory->getAmountOfSeats();
        $vehicleCategoryDTO->amountOfWheelChairs = $vehicleCategory->getAmountOfWheelChairs();
        return $vehicleCategoryDTO;
    }

    /**
     * @param $vehicleCategories
     * @return array
     */
    public function vehicleCategoriesToVehicleCategoryListDTOs($vehicleCategories) {
        $dtoArray = array();
        foreach($vehicleCategories as $vehicleCategorie) {
            $dtoArray[] = $this->toVehicleCategoryListDTO($vehicleCategorie);
        }
        return $dtoArray;
    }

    /**
     * @param VehicleCategory $vehicleCategory
     * @return VehicleCategoryListDTO
     */
    public function toVehicleCategoryListDTO(VehicleCategory $vehicleCategory) {
        $vehicleCategoryListDTO = new VehicleCategoryListDTO();
        $vehicleCategoryListDTO->id = $vehicleCategory->getId();
        $vehicleCategoryListDTO->name = $vehicleCategory->getName();
        $vehicleCategoryListDTO->amountOfSeats = $vehicleCategory->getAmountOfSeats();
        $vehicleCategoryListDTO->amountOfWheelChairs = $vehicleCategory->getAmountOfWheelChairs();
        return $vehicleCategoryListDTO;
    }

} 