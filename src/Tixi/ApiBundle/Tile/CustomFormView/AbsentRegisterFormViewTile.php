<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.04.14
 * Time: 13:07
 */

namespace Tixi\ApiBundle\Tile\CustomFormView;


use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class AbsentRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('subject', 'absent.field.subject',$this->dto->subject);
        $this->basicFormRows[] = new FormRowView('startdate', 'absent.field.startdate',$this->dto->startDate->format('d.m.Y'));
        $this->basicFormRows[] = new FormRowView('enddate', 'absent.field.enddate',$this->dto->endDate->format('d.m.Y'));
    }
}