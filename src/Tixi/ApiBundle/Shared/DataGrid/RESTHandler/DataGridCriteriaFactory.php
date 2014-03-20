<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 22:52
 */

namespace Tixi\ApiBundle\Shared\DataGrid\RESTHandler;


use Doctrine\Common\Collections\Criteria;

class DataGridCriteriaFactory {

    public static function constructCriteriaForPagingOrderingFiltering(DataGridState $state)
    {
        $criteria = Criteria::create();
        $criteria = self::constructFilterCriteria($criteria, $state);
        $criteria->orderBy($state->getOrderBy());
        $criteria->setFirstResult($state->getPage() * $state->getLimit());
        $criteria->setMaxResults($state->getLimit());
        return $criteria;
    }

    public static function constructCriteriaForFiltering(DataGridState $state) {
        $criteria = Criteria::create();
        return self::constructFilterCriteria($criteria, $state);
    }

    private static function constructFilterCriteria(Criteria $criteria, DataGridState $state) {
        if (!is_null($state->getFilterStr()) && !is_null($state->getSourceDTO())) {
            foreach ($state->getSourceDTO() as $field => $value) {
                $criteria->orWhere(Criteria::expr()->contains($field, $state->getFilterStr()));
            }
        }
        return $criteria;
    }


} 