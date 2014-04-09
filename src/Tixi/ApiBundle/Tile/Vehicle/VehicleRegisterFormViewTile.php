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
        $this->basicFormRows[] = new FormRowView('vehicle.field.category',$this->dto->category->getName());
        $this->basicFormRows[] = new FormRowView('vehicle.field.category.amountofseats',$this->dto->category->getAmountOfSeats());
        $this->basicFormRows[] = new FormRowView('vehicle.field.category.amountofwheelchairs',$this->dto->category->getAmountOfWheelChairs());
        $this->basicFormRows[] = new FormRowView('vehicle.field.licencenumber',$this->dto->licenceNumber);
        $this->basicFormRows[] = new FormRowView('vehicle.field.dateoffirstregistration',$this->dto->dateOfFirstRegistration->format('d.m.Y'));
        $this->basicFormRows[] = new FormRowView('vehicle.field.parking',$this->dto->parking);

        if(!empty($this->dto->supervisor)){
            $this->basicFormRows[] = new FormRowView('vehicle.field.supervisor',$this->dto->supervisor->getNameString());
        }

        $this->basicFormRows[] = new FormRowView('vehicle.field.memo',$this->dto->memo);
        $this->basicFormRows[] = new FormRowView('vehicle.field.managementdetails',$this->dto->managementDetails);
    }
}