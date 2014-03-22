<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.03.14
 * Time: 18:40
 */

namespace Tixi\CoreDomain\Shared\GenericEntityFilter;


class GenericAccessQuery {

    protected $selectPart;
    protected $fromPart;
    protected $idPart;

    public function __construct($selectPart, $fromPart, $idPart) {
        $this->selectPart = $selectPart;
        $this->fromPart = $fromPart;
        $this->idPart = $idPart;
    }

    /**
     * @return mixed
     */
    public function getFromPart()
    {
        return $this->fromPart;
    }

    /**
     * @return mixed
     */
    public function getSelectPart()
    {
        return $this->selectPart;
    }

    /**
     * @return mixed
     */
    public function getIdPart()
    {
        return $this->idPart;
    }





} 