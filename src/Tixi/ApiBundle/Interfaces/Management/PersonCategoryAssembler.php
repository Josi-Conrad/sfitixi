<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 17:45
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\CoreDomain\PersonCategory;

/**
 * Class PersonCategoryAssembler
 * @package Tixi\ApiBundle\Interfaces
 */
class PersonCategoryAssembler {

    /**
     * @param PersonCategoryRegisterDTO $personCategoryDTO
     * @return PersonCategory
     */
    public function registerDTOtoNewPersonCategory(PersonCategoryRegisterDTO $personCategoryDTO) {
        $personCategory = PersonCategory::registerPersonCategory(
            $personCategoryDTO->name, $personCategoryDTO->memo);
        return $personCategory;
    }

    /**
     * @param PersonCategoryRegisterDTO $personCategoryDTO
     * @param PersonCategory $personCategory
     * @return PersonCategory
     */
    public function registerDTOtoPersonCategory(PersonCategoryRegisterDTO $personCategoryDTO, PersonCategory $personCategory) {
        $personCategory->updatePersonCategoryData(
            $personCategoryDTO->name, $personCategoryDTO->memo);
        return $personCategory;
    }

    /**
     * @param PersonCategory $personCategory
     * @return PersonCategoryRegisterDTO
     */
    public function personCategoryToPersonCategoryRegisterDTO(PersonCategory $personCategory) {
        $personCategoryDTO = new PersonCategoryRegisterDTO();
        $personCategoryDTO->id = $personCategory->getId();
        $personCategoryDTO->name = $personCategory->getName();
        $personCategoryDTO->memo = $personCategory->getMemo();
        return $personCategoryDTO;
    }

    /**
     * @param $personCategorys
     * @return array
     */
    public function personCategorysToPersonCategoryListDTOs($personCategorys) {
        $dtoArray = array();
        foreach ($personCategorys as $personCategory) {
            $dtoArray[] = $this->personCategorysToPersonCategoryListDTO($personCategory);
        }
        return $dtoArray;
    }

    /**
     * @param PersonCategory $personCategory
     * @return PersonCategoryListDTO
     */
    public function personCategorysToPersonCategoryListDTO(PersonCategory $personCategory) {
        $personCategoryEmbeddedListDTO = new PersonCategoryListDTO();
        $personCategoryEmbeddedListDTO->id = $personCategory->getId();
        $personCategoryEmbeddedListDTO->name = $personCategory->getName();
        return $personCategoryEmbeddedListDTO;
    }

}