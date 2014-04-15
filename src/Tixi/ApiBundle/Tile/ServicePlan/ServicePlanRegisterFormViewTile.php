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
        $this->basicFormRows[] = new FormRowView('startDate','serviceplan.field.startdate',$this->dto->startDate->format('d.m.Y H:i'));
        $this->basicFormRows[] = new FormRowView('endDate','serviceplan.field.enddate',$this->dto->endDate->format('d.m.Y H:i'));
        $this->basicFormRows[] = new FormRowView('memo','serviceplan.field.memo',$this->dto->memo);
    }
}