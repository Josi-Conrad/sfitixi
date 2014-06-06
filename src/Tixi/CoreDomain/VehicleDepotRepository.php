<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.04.14
 * Time: 12:17
 */

namespace Tixi\CoreDomain;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface VehicleDepotRepository
 * @package Tixi\CoreDomain
 */
interface VehicleDepotRepository extends CommonBaseRepository{
    /**
     * @param VehicleDepot $vehicleDepot
     * @return mixed
     */
    public function store(VehicleDepot $vehicleDepot);

    /**
     * @param VehicleDepot $vehicleDepot
     * @return mixed
     */
    public function remove(VehicleDepot $vehicleDepot);

    /**
     * @param $name
     * @return bool
     */
    public function checkIfNameAlreadyExist($name);
} 