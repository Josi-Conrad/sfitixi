<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 11:06
 */

namespace Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties;

/**
 * Class Search
 * @package Tixi\CoreDomain\Shared\GenericEntityFilter\FilterProperties
 */
class Search {

    protected $searchStr;
    protected $entityProperties;

    /**
     * @param $searchString
     * @param array $entityProperties
     */
    public function __construct($searchString, array $entityProperties) {
        $this->searchStr = $searchString;
        $this->entityProperties = $entityProperties;
    }

    /**
     * @return array
     */
    public function getEntityProperties()
    {
        return $this->entityProperties;
    }

    /**
     * @return mixed
     */
    public function getSearchStr()
    {
        return $this->searchStr;
    }
} 