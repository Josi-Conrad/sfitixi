<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\Passenger;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class PassengerRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        /**@var $dto \Tixi\ApiBundle\Interfaces\PassengerRegisterDTO */
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('Fahgrast-Nr', $dto->person_id);
        if (!empty($dto->birthday)) {
            $this->basicFormRows[] = new FormRowView('Alter', $this->getAge($dto->birthday));
        }
        $this->basicFormRows[] = new FormRowView('Vorname', $dto->firstname);
        $this->basicFormRows[] = new FormRowView('Nachname', $dto->lastname);
        $this->basicFormRows[] = new FormRowView('Telefon', $dto->telephone);
        $this->basicFormRows[] = new FormRowView('Strasse', $dto->street);
        $this->basicFormRows[] = new FormRowView('PLZ', $dto->postalCode);
        $this->basicFormRows[] = new FormRowView('Ort', $dto->city);
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