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

/**
 * Class CommonBaseRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class CommonBaseRepositoryDoctrine extends EntityRepository{
    /**
     * @param mixed $id
     * @return null|object
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return null|object
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param Criteria $criteria
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllBy(Criteria $criteria)
    {
        return parent::matching($criteria);
    }

    /**
     * @return mixed
     */
    public function totalNumberOfRecords()
    {
        $qb = parent::createQueryBuilder('e')->select('count(e.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Criteria $criteria
     * @return int
     */
    public function totalNumberOfFilteredRecords(Criteria $criteria)
    {
        return parent::matching($criteria)->count();
    }

} 