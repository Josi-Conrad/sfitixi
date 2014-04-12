<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 10:24
 */

namespace Tixi\CoreDomain\Shared;


use Doctrine\Common\Collections\Criteria;

/**
 * Interface CommonBaseRepository
 * @package Tixi\CoreDomain\Shared
 */
interface CommonBaseRepository {
    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param Criteria $criteria
     * @return mixed
     */
    public function findAllBy(Criteria $criteria);

    /**
     * @return mixed
     */
    public function findAll();

    /**
     * @return mixed
     */
    public function totalNumberOfRecords();

    /**
     * @param Criteria $criteria
     * @return mixed
     */
    public function totalNumberOfFilteredRecords(Criteria $criteria);

} 