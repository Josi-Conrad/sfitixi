<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 10:28
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Insurance;

class InsuranceAssembler {
    public function registerDTOtoNewInsurance(InsuranceRegisterDTO $dto) {
        $insurance = Insurance::registerInsurance($dto->name);
        return $insurance;
    }

    public function registerDTOtoInsurance(Insurance $insurance, InsuranceRegisterDTO $dto) {
        $insurance->updateData($dto->name);
    }

    public function toInsuranceRegisterDTO(Insurance $insurance) {
        $insuranceDTO = new InsuranceRegisterDTO();
        $insuranceDTO->id = $insurance->getId();
        $insuranceDTO->name = $insurance->getName();
        return $insuranceDTO;
    }

    public function insurancesToInsuranceListDTOs($insurances) {
        $dtoArray = array();
        foreach($insurances as $insurance) {
            $dtoArray[] = $this->toInsuranceListDTO($insurance);
        }
        return $dtoArray;
    }

    public function toInsuranceListDTO(Insurance $isurance) {
        $insuranceListDTO = new HandicapListDTO();
        $insuranceListDTO->id = $isurance->getId();
        $insuranceListDTO->name = $isurance->getName();
        return $insuranceListDTO;
    }
} 