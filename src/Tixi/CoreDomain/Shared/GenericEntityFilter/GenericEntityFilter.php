<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 10:28
 */

namespace Tixi\CoreDomain\Shared\GenericEntityFilter;


use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\OrderBy;
use Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties\Search;

class GenericEntityFilter {

    /**
     * @var GenericAccessQuery
     * retrives a entity consolidated data structure from persistency layer
     * whose properties can be accessed in the form entityName.propertyName
     * and can be mapped back to an entity class object
     */
    protected $accessQuery;
    protected $restrictiveProperties;
    protected $offset;
    protected $limit;
    protected $orderedBy;
    protected $search;

    public function __construct(GenericAccessQuery $accessQuery) {
        $this->accessQuery = $accessQuery;
        $this->restrictiveProperties = null;
        $this->offset = null;
        $this->limit = null;
        $this->orderedBy = null;
        $this->search = null;
    }

    public function setRestrictiveProperties(array $entityProperties) {
        $this->restrictiveProperties = $entityProperties;
    }

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return $this->accessQuery;
    }

    /**
     * @return null
     */
    public function getRestrictiveProperties()
    {
        return $this->restrictiveProperties;
    }


    /**
     * @param null $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param null $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param OrderBy $orderedBy
     */
    public function setOrderedBy(OrderBy $orderedBy)
    {
        $this->orderedBy = $orderedBy;
    }

    /**
     * @return null
     */
    public function getOrderedBy()
    {
        return $this->orderedBy;
    }

    /**
     * @param Search $search
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;
    }

    /**
     * @return null
     */
    public function getSearch()
    {
        return $this->search;
    }
} 