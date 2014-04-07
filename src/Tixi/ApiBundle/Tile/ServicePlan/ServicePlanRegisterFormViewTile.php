<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.04.14
 * Time: 13:07
 */

namespace Tixi\ApiBundle\Tile\ServicePlan;


use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class ServicePlanRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('serviceplan.field.startdate',$this->dto->startDate);
        $this->basicFormRows[] = new FormRowView('serviceplan.field.enddate',$this->dto->endDate);
        $this->basicFormRows[] = new FormRowView('serviceplan.field.memo',$this->dto->memo);
    }

    private function dateToString($date){
        return $date->format('d.m.Y');
    }
}