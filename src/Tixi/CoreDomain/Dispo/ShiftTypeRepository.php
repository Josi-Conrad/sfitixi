<?php

namespace Tixi\CoreDomain\Dispo;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface ShiftTypeRepository
 * @package Tixi\CoreDomain\Dispo
 */
interface ShiftTypeRepository extends CommonBaseRepository {
    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function store(ShiftType $shiftType);

    /**
     * @param ShiftType $shiftType
     * @return mixed
     */
    public function remove(ShiftType $shiftType);

    /**
     * @return ShiftType[]
     */
    public function findAllActive();

    /**
     * @param $name
     * @return mixed
     */
    public function checkIfNameAlreadyExist($name);

}