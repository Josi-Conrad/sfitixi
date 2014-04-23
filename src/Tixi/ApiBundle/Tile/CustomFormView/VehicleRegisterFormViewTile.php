<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\CustomFormView;


use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

/**
 * Class VehicleRegisterFormViewTile
 * @package Tixi\ApiBundle\Tile\CustomFormView
 */
class VehicleRegisterFormViewTile extends AbstractFormViewTile{

    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('name','vehicle.field.name',$this->dto->name);
        $this->basicFormRows[] = new FormRowView('category','vehicle.field.category',$this->dto->category->getName());
        $this->basicFormRows[] = new FormRowView('amountofseats','vehicle.field.category.amountofseats',$this->dto->category->getAmountOfSeats());
        $this->basicFormRows[] = new FormRowView('amountofwheelchairs','vehicle.field.category.amountofwheelchairs',$this->dto->category->getAmountOfWheelChairs());
        $this->basicFormRows[] = new FormRowView('licencenumber','vehicle.field.licencenumber',$this->dto->licenceNumber);
        $this->basicFormRows[] = new FormRowView('dateoffirstregistration','vehicle.field.dateoffirstregistration',$this->dto->dateOfFirstRegistration->format('d.m.Y'));
        $this->basicFormRows[] = new FormRowView('parking','vehicle.field.parking',$this->dto->parking);

        if(!empty($this->dto->supervisor)){
            $this->basicFormRows[] = new FormRowView('supervisor','vehicle.field.supervisor',$this->dto->supervisor->getNameString());
        }

        $this->basicFormRows[] = new FormRowView('memo','vehicle.field.memo',$this->dto->memo);
    }
}