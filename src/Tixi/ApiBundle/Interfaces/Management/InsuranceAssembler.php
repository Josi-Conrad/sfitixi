<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 10:28
 */

namespace Tixi\ApiBundle\Interfaces\Management;


use Tixi\CoreDomain\Insurance;

/**
 * Class InsuranceAssembler
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class InsuranceAssembler {
    /**
     * @param InsuranceRegisterDTO $dto
     * @return Insurance
     */
    public function registerDTOtoNewInsurance(InsuranceRegisterDTO $dto) {
        $insurance = Insurance::registerInsurance($dto->name);
        return $insurance;
    }

    /**
     * @param Insurance $insurance
     * @param InsuranceRegisterDTO $dto
     */
    public function registerDTOtoInsurance(Insurance $insurance, InsuranceRegisterDTO $dto) {
        $insurance->updateData($dto->name);
    }

    /**
     * @param Insurance $insurance
     * @return InsuranceRegisterDTO
     */
    public function toInsuranceRegisterDTO(Insurance $insurance) {
        $insuranceDTO = new InsuranceRegisterDTO();
        $insuranceDTO->id = $insurance->getId();
        $insuranceDTO->name = $insurance->getName();
        return $insuranceDTO;
    }

    /**
     * @param $insurances
     * @return array
     */
    public function insurancesToInsuranceListDTOs($insurances) {
        $dtoArray = array();
        foreach($insurances as $insurance) {
            $dtoArray[] = $this->toInsuranceListDTO($insurance);
        }
        return $dtoArray;
    }

    /**
     * @param Insurance $isurance
     * @return HandicapListDTO
     */
    public function toInsuranceListDTO(Insurance $isurance) {
        $insuranceListDTO = new HandicapListDTO();
        $insuranceListDTO->id = $isurance->getId();
        $insuranceListDTO->name = $isurance->getName();
        return $insuranceListDTO;
    }
} 