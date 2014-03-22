<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 10:30
 */

namespace Tixi\CoreDomainBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Tixi\ApiBundle\Shared\Paginator;
use Tixi\CoreDomain\Shared\CommonCriteriaFactory;

class CommonBaseRepositoryDoctrine extends EntityRepository{

    public function find($id)
    {
        return parent::find($id);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function findAll()
    {
        return parent::findAll();
    }

    public function findAllBy(Criteria $criteria)
    {
        return parent::matching($criteria);
    }

    public function totalNumberOfRecords()
    {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function totalNumberOfFilteredRecords(Criteria $criteria)
    {
        return parent::matching($criteria)->count();
    }

} 