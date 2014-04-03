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
        $this->basicFormRows[] = new FormRowView('Beschreibung',$this->dto->subject);
        $this->basicFormRows[] = new FormRowView('Start Datum',$this->dto->startDate);
        $this->basicFormRows[] = new FormRowView('End Datum',$this->dto->endDate);
    }
}