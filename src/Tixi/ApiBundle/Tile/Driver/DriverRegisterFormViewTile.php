<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\Driver;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class DriverRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        /**@var $dto \Tixi\ApiBundle\Interfaces\DriverRegisterDTO */
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('driver.field.id', $dto->person_id);
        if (!empty($dto->birthday)) {
            $this->basicFormRows[] = new FormRowView('person.field.age', $this->getAge($dto->birthday));
        }
        $this->basicFormRows[] = new FormRowView('person.field.firstname', $dto->firstname);
        $this->basicFormRows[] = new FormRowView('person.field.lastname', $dto->lastname);
        $this->basicFormRows[] = new FormRowView('person.field.telephone', $dto->telephone);
        $this->basicFormRows[] = new FormRowView('person.field.email', $dto->email);

        $this->expandedFormRows[] = new FormRowView('address.field.street', $dto->street);
        $this->expandedFormRows[] = new FormRowView('address.field.postalcode', $dto->postalCode);
        $this->expandedFormRows[] = new FormRowView('address.field.city', $dto->city);
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