<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.04.14
 * Time: 13:07
 */

namespace Tixi\ApiBundle\Tile\Absent;


use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class AbsentRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('absent.field.subject',$this->dto->subject);
        $this->basicFormRows[] = new FormRowView('absent.field.startdate',$this->dto->startDate->format('d.m.Y'));
        $this->basicFormRows[] = new FormRowView('absent.field.enddate',$this->dto->endDate->format('d.m.Y'));
    }
}