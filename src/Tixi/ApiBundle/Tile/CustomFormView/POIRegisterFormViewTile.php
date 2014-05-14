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
use Tixi\CoreDomain\POI;

/**
 * Class POIRegisterFormViewTile
 * @package Tixi\ApiBundle\Tile\CustomFormView
 */
class POIRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        $dto = $this->dto;
        $this->basicFormRows[] = new FormRowView('id', 'poi.field.id', $dto->id);

        $this->basicFormRows[] = new FormRowView('name', 'poi.field.name', $dto->name);
        $this->basicFormRows[] = new FormRowView('department', 'poi.field.department', $dto->department);
        $this->basicFormRows[] = new FormRowView('telephone', 'poi.field.telephone', $dto->telephone);
        $this->basicFormRows[] = new FormRowView('street', 'address.field.street', $dto->street);
        $this->basicFormRows[] = new FormRowView('postalcode', 'address.field.postalcode', $dto->postalCode);
        $this->basicFormRows[] = new FormRowView('city', 'address.field.city', $dto->city);
        $this->basicFormRows[] = new FormRowView('keywords', 'poi.field.keywords', POI::constructKeywordsString($dto->keywords));

        $this->expandedFormRows[] = new FormRowView('comment', 'poi.field.comment', $dto->comment);
        $this->expandedFormRows[] = new FormRowView('details', 'poi.field.details', $dto->details);
    }
}