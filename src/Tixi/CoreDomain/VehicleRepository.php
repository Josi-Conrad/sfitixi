<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 12:17
 */

namespace Tixi\CoreDomain;


use Symfony\Component\Security\Core\User\User;
use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface VehicleRepository
 * @package Tixi\CoreDomain
 */
interface VehicleRepository extends CommonBaseRepository{
    /**
     * @param Vehicle $vehicle
     * @return mixed
     */
    public function store(Vehicle $vehicle);

    /**
     * @param Vehicle $vehicle
     * @return mixed
     */
    public function remove(Vehicle $vehicle);

    /**
     * @param VehicleCategory $category
     * @return mixed
     */
    public function getAmountByVehicleCategory(VehicleCategory $category);

    /**
     * @param VehicleDepot $depot
     * @return mixed
     */
    public function getAmountByVehicleDepot(VehicleDepot $depot);
}