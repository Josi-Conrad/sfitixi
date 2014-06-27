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

/**
 * Class DataGridInputState
 * @package Tixi\ApiBundle\Shared\DataGrid
 */
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

    //if true, all restrictive properties are disabled
    protected $showAll = false;

    /**
     * @param $sourceDTO
     * @param null $orderByField
     * @param null $orderByDirection
     * @param null $page
     * @param null $limit
     * @param null $filterStr
     * @param bool $partial
     * @param bool $showAll
     */
    public function __construct($sourceDTO, $orderByField=null, $orderByDirection=null, $page=null, $limit=null, $filterStr=null, $partial=false, $showAll=false) {
        $this->sourceDTO = $sourceDTO;
        $this->orderByField = $orderByField;
        $this->orderByDirection = $orderByDirection;
        $this->page = (!is_null($page)) ? $page : $this->defaultStartPage;
        $this->limit = (!is_null($limit)) ? $limit : $this->defaultLimitPerPage;
        $this->filterStr = $filterStr;
        $this->partial = $partial;
        $this->showAll = $showAll;
    }

    /**
     * @return bool
     */
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

    public function isInShowAllState() {
        return $this->showAll;
    }
} 