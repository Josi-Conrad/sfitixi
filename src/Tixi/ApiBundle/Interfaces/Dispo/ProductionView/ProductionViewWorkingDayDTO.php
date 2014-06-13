<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 10:49
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\ProductionView;

/**
 * Class ProductionViewWorkingDayDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo\ProductionView
 */
class ProductionViewWorkingDayDTO {
    public $id;
    public $comment;
    public $dateString;
    public $weekDayString;

    public $workingShifts;

    public function getWorkingShiftPerId($id) {
        $toReturn = null;
        foreach($this->workingShifts as $workingShift) {
            if($workingShift->id===$id) {
                $toReturn = $workingShift;
                break;
            }
        }
        return $toReturn;
    }
}