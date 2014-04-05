<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 10:33
 */

namespace Tixi\ApiBundle\Interfaces\Dispo;


use Doctrine\Common\Collections\ArrayCollection;

class ShiftSelectionDTO {

    public $selectionId;
    public $shiftSelection;

    public function __construct() {
        $this->shiftSelection = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSelectionId()
    {
        return $this->selectionId;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getShiftSelection()
    {
        return $this->shiftSelection;
    }



} 