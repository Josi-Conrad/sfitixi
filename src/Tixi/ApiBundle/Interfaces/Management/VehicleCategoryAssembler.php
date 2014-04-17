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
    public function registerDTOtoNewVehicleCategory(VehicleCategoryRegisterDTO $dto) {
        $vehicleCategory = VehicleCategory::registerVehicleCategory($dto->name, $dto->amountOfSeats, $dto->amountOfWheelChairs);
        return $vehicleCategory;
    }

    public function registerDTOtoVehicleCategory(VehicleCategory $vehicleCategory, VehicleCategoryRegisterDTO $dto) {
        $vehicleCategory->updateData($dto->name, $dto->amountOfSeats, $dto->amountOfWheelChairs);
    }

    public function toVehicleCategoryRegisterDTO(VehicleCategory $vehicleCategory) {
        $vehicleCategoryDTO = new VehicleCategoryRegisterDTO();
        $vehicleCategoryDTO->id = $vehicleCategory->getId();
        $vehicleCategoryDTO->name = $vehicleCategory->getName();
        $vehicleCategoryDTO->amountOfSeats = $vehicleCategory->getAmountOfSeats();
        $vehicleCategoryDTO->amountOfWheelChairs = $vehicleCategory->getAmountOfWheelChairs();
        return $vehicleCategoryDTO;
    }

    public function vehicleCategoriesToVehicleCategoryListDTOs($vehicleCategories) {
        $dtoArray = array();
        foreach($vehicleCategories as $vehicleCategorie) {
            $dtoArray[] = $this->toVehicleCategoryListDTO($vehicleCategorie);
        }
        return $dtoArray;
    }

    public function toVehicleCategoryListDTO(VehicleCategory $vehicleCategory) {
        $vehicleCategoryListDTO = new VehicleCategoryListDTO();
        $vehicleCategoryListDTO->id = $vehicleCategory->getId();
        $vehicleCategoryListDTO->name = $vehicleCategory->getName();
        $vehicleCategoryListDTO->amountOfSeats = $vehicleCategory->getAmountOfSeats();
        $vehicleCategoryListDTO->amountOfWheelChairs = $vehicleCategory->getAmountOfWheelChairs();
        return $vehicleCategoryListDTO;
    }

} 