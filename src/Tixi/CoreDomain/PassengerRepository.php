<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface PassengerRepository
 * @package Tixi\CoreDomain
 */
interface PassengerRepository extends CommonBaseRepository{
    /**
     * @param Passenger $passenger
     * @return mixed
     */
    public function store(Passenger $passenger);

    /**
     * @param Passenger $passenger
     * @return mixed
     */
    public function remove(Passenger $passenger);

}