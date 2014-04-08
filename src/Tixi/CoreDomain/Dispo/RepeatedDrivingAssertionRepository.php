<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 08.04.14
 * Time: 21:08
 */

namespace Tixi\CoreDomain\Dispo;


use Tixi\CoreDomain\Shared\CommonBaseRepository;

interface RepeatedDrivingAssertionRepository extends CommonBaseRepository {

    public function store(RepeatedDrivingAssertion $assertion);

    public function remove(RepeatedDrivingAssertion $assertion);

} 