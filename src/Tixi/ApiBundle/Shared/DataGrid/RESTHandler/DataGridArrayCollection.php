<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 22:06
 */

namespace Tixi\ApiBundle\Shared\DataGrid\RESTHandler;


class DataGridHandlerArrayCollection extends DataGridHandler{

    public function __construct($source) {
        parent::__construct($source);
    }

    public function findAllBy(DataGridState $state)
    {
        return $this->source->matching(DataGridCriteriaFactory::constructCriteriaForPagingOrderingFiltering($state));
    }

    public function totalNumberOfRecords(DataGridState $state)
    {
        $toReturn = null;
        if($state->hasFilter()) {
            $toReturn = $this->source->matching(DataGridCriteriaFactory::constructCriteriaForFiltering($state))->count();
        }else {
            $toReturn = $this->source->count();
        }
        return $toReturn;
    }
}