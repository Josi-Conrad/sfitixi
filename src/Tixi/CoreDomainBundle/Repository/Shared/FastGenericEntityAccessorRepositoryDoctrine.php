<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 10:29
 */

namespace Tixi\CoreDomainBundle\Repository\Shared;


use Doctrine\ORM\EntityManager;
use Tixi\CoreDomain\Shared\FastGenericEntityAccessorRepository;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * In usage of the DataGrid, generic access on entities
 * Class FastGenericEntityAccessorRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository\Shared
 */
class FastGenericEntityAccessorRepositoryDoctrine implements FastGenericEntityAccessorRepository {

    protected static $FINDBYTYPE = 'findby';
    protected static $TOTALTYPE = 'total';

    /**@var $entitManager EntityManager */
    protected $entityManager;

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function findByFilter(GenericEntityFilter $filter) {
        $entityArray = array();
        $dqlQueryString = $this->createQueryDQL($filter, self::$FINDBYTYPE);
        if ('' !== $dqlQueryString) {
            $query = $this->entityManager->createQuery($dqlQueryString);
            $query->setParameters($this->createParametersArray($filter));
            if (null !== $filter->getLimit()) {
                $query->setMaxResults($filter->getLimit());
            }
            if (null !== $filter->getOffset()) {
                $query->setFirstResult($filter->getOffset());
            }
            $entityArray = $query->getResult();
        }
        return $entityArray;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return int|mixed
     */
    public function findTotalAmountByFilter(GenericEntityFilter $filter) {
        $totalAmount = 0;
        $dqlQueryString = $this->createQueryDQL($filter, self::$TOTALTYPE);
        if ('' !== $dqlQueryString) {
            $query = $this->entityManager->createQuery($dqlQueryString);
            $query->setParameters($this->createParametersArray($filter));
            $totalAmount = $query->getSingleScalarResult();
        }
        return $totalAmount;
    }

    /**
     * @param GenericEntityFilter $filter
     */
    public function totalNumberOfRecords(GenericEntityFilter $filter) {

    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param GenericEntityFilter $filter
     * @param $type
     * @return string
     */
    protected function createQueryDQL(GenericEntityFilter $filter, $type) {
        $queryStringDQL = '';
        $accessQueryDQL = $this->createAccessQueryDQL($filter->getAccessQuery(), $type);
        if ('' !== $accessQueryDQL) {
            $queryStringDQL .= $accessQueryDQL;
            $whereDQL = $this->createWhereDQL($filter);
            if ('' !== $whereDQL) {
                $queryStringDQL .= ' ' . $whereDQL;
            }
            if ($type === self::$FINDBYTYPE) {
                $orderByDQL = $this->createOrderByDQL($filter);
                if ('' !== $orderByDQL) {
                    $queryStringDQL .= ' ' . $orderByDQL;
                }
            }
        }
        return $queryStringDQL;
    }

    /**
     * @param GenericAccessQuery $accessQuery
     * @param $type
     * @return string
     */
    protected function createAccessQueryDQL(GenericAccessQuery $accessQuery, $type) {
        $accessQueryDQL = '';
        if ($type === self::$FINDBYTYPE) {
            $accessQueryDQL = ('SELECT ' . $accessQuery->getSelectPart() . ' FROM ' . $accessQuery->getFromPart());
        } else if ($type === 'total') {
            $accessQueryDQL = 'SELECT COUNT(' . $accessQuery->getIdPart() . ') FROM ' . $accessQuery->getFromPart();
        }
        return $accessQueryDQL;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return string
     * Bad code. Should be done recusively via abstraction.
     */
    protected function createWhereDQL(GenericEntityFilter $filter) {
        $sqlWhere = '';
        $dqlRestrictive = $this->createRestrictivePropertyDQL($filter);
        $dqlSearch = $this->createSearchDQL($filter);
        if ('' !== $dqlRestrictive || '' !== $dqlSearch) {
            $sqlWhere = 'WHERE ';
            if ('' !== $dqlRestrictive && '' !== $dqlSearch) {
                $dqlRestrictive = substr_replace($dqlRestrictive, "", -1);
                $dqlRestrictive .= ' AND ';
                $dqlSearch .= ')';
            }
            $sqlWhere .= $dqlRestrictive . $dqlSearch;
        }
        return $sqlWhere;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return string
     */
    protected function createRestrictivePropertyDQL(GenericEntityFilter $filter) {
        $restrictiveProperties = $filter->getRestrictiveProperties();
        $dqlRestrictive = '';
        if (!empty($restrictiveProperties)) {
            $dqlRestrictive .= '(';
            $i = 0;
            foreach ($restrictiveProperties as $index => $property) {
                if ($index > 0) {
                    $dqlRestrictive .= ' AND ';
                }
                $dqlRestrictive .= $property->getEntityPropertyString() . ' ' . $property->getComparingOperator() . ' :restrictiveProperty' . $i;
                $i++;
            }
            $dqlRestrictive .= ')';
        }
        return $dqlRestrictive;
    }

    protected function createSearchDQL(GenericEntityFilter $filter) {
        $seachProperty = $filter->getSearch();
        $dqlSearch = '';
        if (!empty($seachProperty)) {
            $dqlSearch = '(';
            $entryProperties = $seachProperty->getEntityProperties();
            foreach ($entryProperties as $index => $property) {
                if ($index > 0) {
                    $dqlSearch .= ' OR ';
                }
                $dqlSearch .= $property->getEntityPropertyString() . ' LIKE :searchStr';
            }
            $dqlSearch .= ')';
        }
        return $dqlSearch;
    }

    protected function createOrderByDQL(GenericEntityFilter $filter) {
        $orderByProperty = $filter->getOrderedBy();
        $dqlOrderBy = '';
        if (!empty($orderByProperty)) {
            $dqlOrderBy = 'ORDER BY ' . $orderByProperty->getEntityProperty()->getEntityPropertyString() . ' ' . $orderByProperty->getDirection();

        }
        return $dqlOrderBy;
    }

    protected function createLikedSerachString($searchString) {
        return '%' . $searchString . '%';

    }

    protected function createParametersArray(GenericEntityFilter $filter) {
        $parameters = array();
        if (null !== $filter->getRestrictiveProperties()) {
            $i = 0;
            foreach ($filter->getRestrictiveProperties() as $property) {
                $parameters['restrictiveProperty' . $i] = $property->getPropertyValue();
                $i++;
            }
        }
        if ($filter->getSearch()) {
            $parameters['searchStr'] = $this->createLikedSerachString($filter->getSearch()->getSearchStr());
        }
        return $parameters;
    }


}