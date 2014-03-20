<?php

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface PersonRepository extends CommonBaseRepository{

    public function store(Person $person);

    public function remove(Person $person);
} 