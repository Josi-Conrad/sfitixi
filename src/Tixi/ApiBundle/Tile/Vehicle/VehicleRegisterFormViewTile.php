<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\Vehicle;


use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class VehicleRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('Fahrzeugname',$this->dto->name);
        $this->basicFormRows[] = new FormRowView('Kennzeichen',$this->dto->licenceNumber);
        $this->basicFormRows[] = new FormRowView('Inverkehrssetzung',$this->dto->dateOfFirstRegistration);
        $this->basicFormRows[] = new FormRowView('Parkplatz- bezeichnung',$this->dto->parkingLotNumber);
        $this->basicFormRows[] = new FormRowView('Fahrzeugkategorie',$this->dto->category);

//        $this->expandedFormRows[] = new FormRowView('Fahrzeugkategorie',$this->dto->category);
    }
}