<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\CustomFormView;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Person;

/**
 * Class DriverRegisterFormViewTile
 * @package Tixi\ApiBundle\Tile\CustomFormView
 */
class DriverRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        /**@var $dto \Tixi\ApiBundle\Interfaces\DriverRegisterDTO */
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('id', 'driver.field.id', $dto->person_id);
        $this->basicFormRows[] = new FormRowView('gender', 'person.field.gender', Person::constructGenderString($dto->gender));
        $this->basicFormRows[] = new FormRowView('firstname', 'person.field.firstname', $dto->firstname);
        $this->basicFormRows[] = new FormRowView('lastname', 'person.field.lastname', $dto->lastname);
        $this->basicFormRows[] = new FormRowView('telephone', 'person.field.telephone', $dto->telephone);
        $this->basicFormRows[] = new FormRowView('email', 'person.field.email', $dto->email);
        $this->basicFormRows[] = new FormRowView('wheelChairAttendance', 'driver.field.wheelchair',
            Driver::constructWheelChairAttendanceString($dto->wheelChairAttendance));
        $this->expandedFormRows[] = new FormRowView('address','address.field.lookahead',$dto->lookaheadaddress->addressDisplayName);
        if (!empty($dto->entryDate)) {
            $this->expandedFormRows[] = new FormRowView('entrydate', 'person.field.entrydate', $dto->entryDate->format('d.m.Y'));
        }
        if (!empty($dto->birthday)) {
            $this->expandedFormRows[] = new FormRowView('birthday', 'person.field.birthday', $dto->birthday->format('d.m.Y'));
        }
        if (!empty($dto->birthday)) {
            $this->expandedFormRows[] = new FormRowView('age', 'person.field.age', $this->getAge($dto->birthday));
        }
        $this->expandedFormRows[] = new FormRowView('extraminutes', 'person.field.extraminutes', $dto->extraMinutes);
    }

    /**
     * @param $birthday
     * @return string
     */
    private function getAge($birthday) {
        $dateNow = new \DateTime();
        $dateIntervall = $dateNow->diff($birthday);
        return $dateIntervall->y;
    }
}