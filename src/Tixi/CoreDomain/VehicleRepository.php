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

interface VehicleRepository extends CommonBaseRepository{

    public function store(Vehicle $vehicle);

    public function remove(Vehicle $vehicle);
} 