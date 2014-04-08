<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 08.04.14
 * Time: 21:09
 */

namespace Tixi\CoreDomainBundle\Repository\Dispo;


use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertion;
use Tixi\CoreDomain\Dispo\RepeatedDrivingAssertionRepository;
use Tixi\CoreDomainBundle\Repository\CommonBaseRepositoryDoctrine;

class RepeatedDrivingAssertionRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements RepeatedDrivingAssertionRepository{

    public function store(RepeatedDrivingAssertion $assertion)
    {
        $this->getEntityManager()->persist($assertion);
    }

    public function remove(RepeatedDrivingAssertion $assertion)
    {
        $this->getEntityManager()->remove($assertion);
    }
}