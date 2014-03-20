<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 21:06
 */

namespace Tixi\ApiBundle\Shared\DataGrid\RESTHandler;
use Doctrine\Common\Collections\ArrayCollection;


abstract class DataGridHandler {

    const REPOSITORY_TYPE = 'repositoryType';
    const ARRAYCOLLECTION_Type = 'arrayCollectionType';

    protected $source;

    protected function __construct($source) {
        $this->source = $source;
    }

    public static function create($type, $source) {
        if(empty($source)) {
            throw new \Exception('source must be defined');
        }
        if($type==self::REPOSITORY_TYPE) {
            return new DataGridCommonRepository($source);
        }else if($type==self::ARRAYCOLLECTION_Type) {
            return new DataGridHandlerArrayCollection($source);
        }else {
            throw new \Exception('type unknown');
        }
    }

    public abstract function findAllBy(DataGridState $state);

    public abstract function totalNumberOfRecords(DataGridState $state);
} 