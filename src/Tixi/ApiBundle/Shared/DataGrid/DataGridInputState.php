<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 22:14
 */

namespace Tixi\ApiBundle\Shared\DataGrid;


use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\ApiBundle\Shared\Paginator;

class DataGridInputState {

    protected $defaultStartPage = 1;
    protected $defaultLimitPerPage = 15;

    protected $orderByField = null;
    protected $orderByDirection = null;
    protected $page = null;
    protected $limit = null;
    protected $filterStr = null;
    protected $sourceDTO = null;
    protected $partial = null;

    public function __construct($sourceDTO, $orderByField=null, $orderByDirection=null, $page=null, $limit=null, $filterStr=null, $partial=false) {
        $this->sourceDTO = $sourceDTO;
        $this->orderByField = $orderByField;
        $this->orderByDirection = $orderByDirection;
        $this->page = (!is_null($page)) ? $page : $this->defaultStartPage;
        $this->limit = (!is_null($limit)) ? $limit : $this->defaultLimitPerPage;
        $this->filterStr = $filterStr;
        $this->partial = $partial;
    }

//    public static function createByRequest(Request $request, DataGridSourceClass $sourceDTO) {
//        $page = $request->get('page');
//        $limit = $request->get('limit');
//        $orderByField = $request->get('orderbyfield');
//        $orderByDirection = $request->get('orderbydirection');
//        $filterstr = $request->get('filterstr');
//        $correctedPage = Paginator::adjustPageForPagination($page);
//        return new DataGridInputState($sourceDTO, $orderByField, $orderByDirection, $correctedPage, $limit, $filterstr);
//    }
//
//    public static function createByParamFetcher(ParamFetcherInterface $paramFetcher, DataGridSourceClass $sourceDTO) {
//        $page = $paramFetcher->get('page');
//        $limit = $paramFetcher->get('limit');
//        $orderByField = $paramFetcher->get('orderbyfield');
//        $orderByDirection = $paramFetcher->get('orderbydirection');
//        $filterstr = $paramFetcher->get('filterstr');
//        $correctedPage = Paginator::adjustPageForPagination($page);
//        return new DataGridInputState($sourceDTO, $orderByField, $orderByDirection, $correctedPage, $limit, $filterstr);
//    }

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
     * @return null
     */
    public function getOrderByDirection()
    {
        return $this->orderByDirection;
    }

    /**
     * @return null
     */
    public function getOrderByField()
    {
        return $this->orderByField;
    }



    /**
     * @return int|null
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return DataGridSourceClass
     */
    public function getSourceDTO()
    {
        return $this->sourceDTO;
    }

    /**
     * @return bool|null
     */
    public function isPartial()
    {
        return $this->partial;
    }
} 