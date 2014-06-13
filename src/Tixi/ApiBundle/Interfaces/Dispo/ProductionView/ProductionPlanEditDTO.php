<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 25.05.14
 * Time: 23:00
 */

namespace Tixi\ApiBundle\Interfaces\Dispo\ProductionView;


/**
 * Class ProductionPlanEditDTO
 * @package Tixi\ApiBundle\Interfaces\Dispo\ProductionView
 */
class ProductionPlanEditDTO {

    public $workingMonthId;
    public $dateString;
    public $memo;
    public $workingDays = array();
    public $workingShiftsDisplayNames = array();

    /**
     * @param $id
     * @return null
     */
    public function getWorkingDayPerId($id) {
        $toReturn = null;
        foreach($this->workingDays as $workingDay) {
            if($workingDay->id===$id) {
                $toReturn = $workingDay;
                break;
            }

        }
        return $toReturn;
    }

} 