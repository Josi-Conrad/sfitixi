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
        $this->basicFormRows[] = new FormRowView('vehicle.field.name',$this->dto->name);
        $this->basicFormRows[] = new FormRowView('vehicle.field.licencenumber',$this->dto->licenceNumber);
        $this->basicFormRows[] = new FormRowView('vehicle.field.dateoffirstregistration',$this->dto->dateOfFirstRegistration);
        $this->basicFormRows[] = new FormRowView('vehicle.field.parkinglotnumber',$this->dto->parkingLotNumber);
        $this->basicFormRows[] = new FormRowView('vehicle.field.category',$this->dto->category);

//        $this->expandedFormRows[] = new FormRowView('Fahrzeugkategorie',$this->dto->category);
    }
}