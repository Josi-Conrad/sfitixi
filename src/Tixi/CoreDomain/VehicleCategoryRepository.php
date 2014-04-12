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
 * Interface VehicleCategoryRepository
 * @package Tixi\CoreDomain
 */
interface VehicleCategoryRepository extends CommonBaseRepository{
    /**
     * @param VehicleCategory $vehicleCategory
     * @return mixed
     */
    public function store(VehicleCategory $vehicleCategory);

    /**
     * @param VehicleCategory $vehicleCategory
     * @return mixed
     */
    public function remove(VehicleCategory $vehicleCategory);
} 