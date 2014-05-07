<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface AddressRepository
 * @package Tixi\CoreDomain
 */
interface AddressRepository extends CommonBaseRepository{
    /**
     * @param Address $address
     * @return mixed
     */
    public function store(Address $address);

    /**
     * @param Address $address
     * @return mixed
     */
    public function remove(Address $address);

    /**
     * @return Address[]
     */
    public function findAddressesWithoutCoordinates();
}