<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 22:06
 */

namespace Tixi\ApiBundle\Shared\DataGrid\RESTHandler;


class DataGridCommonRepository extends DataGridHandler{

    public function findAllBy(DataGridState $state)
    {
        return $this->source->findAllBy(DataGridCriteriaFactory::constructCriteriaForPagingOrderingFiltering($state));
    }

    public function totalNumberOfRecords(DataGridState $state)
    {
        $toReturn = null;
        if($state->hasFilter()) {
            $toReturn = $this->source->totalNumberOfFilteredRecords(DataGridCriteriaFactory::constructCriteriaForFiltering($state));
        }else {
            $toReturn = $this->source->totalNumberOfRecords();
        }
        return $toReturn;
    }
}