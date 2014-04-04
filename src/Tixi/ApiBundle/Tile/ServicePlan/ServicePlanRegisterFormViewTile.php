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
        $this->basicFormRows[] = new FormRowView('Service Start',$this->dateToString($this->dto->startDate));
        $this->basicFormRows[] = new FormRowView('Service Ende',$this->dateToString($this->dto->endDate));
        $this->basicFormRows[] = new FormRowView('Notiz',$this->dto->memo);
    }

    private function dateToString($date){
        return $date->format('d.m.Y');
    }
}