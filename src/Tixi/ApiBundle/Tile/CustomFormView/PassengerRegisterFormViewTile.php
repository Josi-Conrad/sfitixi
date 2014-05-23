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
use Tixi\CoreDomain\Passenger;
use Tixi\CoreDomain\Person;

/**
 * Class PassengerRegisterFormViewTile
 * @package Tixi\ApiBundle\Tile\CustomFormView
 */
class PassengerRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        /**@var $dto \Tixi\ApiBundle\Interfaces\PassengerRegisterDTO */
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('id', 'passenger.field.id', $dto->person_id);
        $this->basicFormRows[] = new FormRowView('gender', 'person.field.gender', Person::constructGenderString($dto->gender));
        $this->basicFormRows[] = new FormRowView('firstname', 'person.field.firstname', $dto->firstname);
        $this->basicFormRows[] = new FormRowView('lastname', 'person.field.lastname', $dto->lastname);
        $this->basicFormRows[] = new FormRowView('telephone', 'person.field.telephone', $dto->telephone);
        if (!empty($dto->building)) {
            $this->basicFormRows[] = new FormRowView('building', 'address.field.building', $dto->building);
        }
        $this->basicFormRows[] = new FormRowView('address', 'address.field.lookahead', $dto->lookaheadaddress->addressDisplayName);
        $this->basicFormRows[] = new FormRowView('isInWheelChair', 'passenger.field.isinwheelchair',
            Passenger::constructIsInWheelChairString($dto->isInWheelChair));
        $this->basicFormRows[] = new FormRowView('hasMonthlyBilling', 'passenger.field.payment',
            Passenger::constructMonthlyBillingString($dto->hasMonthlyBilling));
        $this->basicFormRows[] = new FormRowView('insurances', 'passenger.field.insurance',
            Passenger::constructInsurancesString($dto->insurances));
        $this->basicFormRows[] = new FormRowView('notice', 'passenger.field.notice', $dto->notice);

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