<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 22:14
 */

namespace Tixi\ApiBundle\Shared\DataGrid\RESTHandler;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Tixi\ApiBundle\Shared\Paginator;

class DataGridState {
    private $orderBy = null;
    private $page = null;
    private $limit = null;
    private $filterStr = null;
    private $sourceDTO = null;

    private function __construct($orderBy=null, $page=null, $limit=null, $filterStr=null, $sourceDTO=null) {
        $this->orderBy = (!is_null($orderBy)) ? $orderBy : array();
        $this->page = (!is_null($page)) ? $page : 1;
        $this->limit = (!is_null($limit)) ? $limit : 15;
        $this->filterStr = $filterStr;
        $this->sourceDTO = $sourceDTO;
    }

    public static function createByParamFetcher(ParamFetcherInterface $paramFetcher, $sourceDTO) {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $orderByField = $paramFetcher->get('orderbyfield');
        $orderByDirection = $paramFetcher->get('orderbydirection');
        $filterstr = $paramFetcher->get('filterstr');
        $orderBy = array();
        if(!empty($orderByField) && !empty($orderByDirection)) {
            $orderBy = array($orderByField=>$orderByDirection);
        }
        $correctedPage = Paginator::adjustPageForPagination($page);
        return new DataGridState($orderBy, $correctedPage, $limit, $filterstr, $sourceDTO);
    }

    public function hasFilter() {
        return (!is_null($this->filterStr) && !is_null($this->sourceDTO));
    }

    /**
     * @return null
     */
    public function getFilterStr()
    {
        return $this->filterStr;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return array|null
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @return int|null
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return null
     */
    public function getSourceDTO()
    {
        return $this->sourceDTO;
    }


} 