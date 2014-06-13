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
    /**
     * @return mixed|void
     */
    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('name','vehicle.field.name',$this->dto->name);
        $this->basicFormRows[] = new FormRowView('category','vehicle.field.category',$this->dto->category->getName());
        $this->basicFormRows[] = new FormRowView('amountofseats','vehiclecategory.field.amountofseats',$this->dto->category->getAmountOfSeats());
        $this->basicFormRows[] = new FormRowView('amountofwheelchairs','vehiclecategory.field.amountofwheelchairs',$this->dto->category->getAmountOfWheelChairs());
        if(!empty($this->dto->depot)){
            $this->basicFormRows[] = new FormRowView('depot','vehicle.field.depot',$this->dto->depot->getNameString());
        }
        $this->basicFormRows[] = new FormRowView('parking','vehicle.field.parking',$this->dto->parking);
        $this->basicFormRows[] = new FormRowView('licencenumber','vehicle.field.licencenumber',$this->dto->licenceNumber);
        $this->basicFormRows[] = new FormRowView('dateoffirstregistration','vehicle.field.dateoffirstregistration',$this->dto->dateOfFirstRegistration->format('d.m.Y'));
        if(!empty($this->dto->supervisor)){
            $this->basicFormRows[] = new FormRowView('supervisor','vehicle.field.supervisor',$this->dto->supervisor->getNameString());
        }

        $this->basicFormRows[] = new FormRowView('memo','vehicle.field.memo',$this->dto->memo);
    }
}