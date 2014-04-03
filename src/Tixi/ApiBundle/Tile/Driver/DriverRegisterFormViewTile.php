<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\Driver;

use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class DriverRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        /**@var $dto \Tixi\ApiBundle\Interfaces\DriverRegisterDTO*/
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('FahrerNr',$dto->person_id);
        $this->basicFormRows[] = new FormRowView('Anrede',$dto->title);
        $this->basicFormRows[] = new FormRowView('Vorname',$dto->firstname);
        $this->basicFormRows[] = new FormRowView('Nachname',$dto->lastname);
        $this->basicFormRows[] = new FormRowView('Telefon',$dto->telephone);
        $this->basicFormRows[] = new FormRowView('E-Mail',$dto->email);
    }
}