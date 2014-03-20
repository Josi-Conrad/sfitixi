<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.03.14
 * Time: 10:24
 */

namespace Tixi\CoreDomain\Shared;


use Doctrine\Common\Collections\Criteria;

interface CommonBaseRepository {

    public function find($id);

    public function findAllBy(Criteria $criteria);

    public function findAll();

    public function totalNumberOfRecords();

    public function totalNumberOfFilteredRecords(Criteria $criteria);

} 