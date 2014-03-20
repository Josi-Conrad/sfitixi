<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface PassengerRepository extends CommonBaseRepository{

    public function store(Passenger $passenger);

    public function remove(Passenger $passenger);

}