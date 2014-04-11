<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\POI;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class POIRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('poi.field.id', $dto->id);

        $this->basicFormRows[] = new FormRowView('poi.field.name', $dto->name);
        $this->basicFormRows[] = new FormRowView('poi.field.department', $dto->department);
        $this->basicFormRows[] = new FormRowView('poi.field.telephone', $dto->telephone);
        $this->basicFormRows[] = new FormRowView('address.field.street', $dto->street);
        $this->basicFormRows[] = new FormRowView('address.field.postalcode', $dto->postalCode);
        $this->basicFormRows[] = new FormRowView('address.field.city', $dto->city);

        $this->expandedFormRows[] = new FormRowView('poi.field.memo', $dto->memo);
        $this->expandedFormRows[] = new FormRowView('poi.field.comment', $dto->comment);
        $this->expandedFormRows[] = new FormRowView('poi.field.details', $dto->details);
    }
}