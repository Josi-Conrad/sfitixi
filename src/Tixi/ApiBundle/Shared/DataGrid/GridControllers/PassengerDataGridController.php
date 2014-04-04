<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\PassengerListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class PassengerDataGridController extends DataGridAbstractController {

    public function getGridIdentifier() {
        return 'passengers';
    }

    public function getGridDisplayTitel() {
        return 'Fahrgast';
    }

    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('Mit Auswahl'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_editbasic', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Editieren', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_absent_new', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Neue Abwesenheit', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_editbasic', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Neuer Fahrauftrag', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_editbasic', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Löschen', true));
        $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_passenger_new'), 'Neuer Fahrgast', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_passenger_get', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new PassengerListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerpassenger');
        $passengers = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->passengersToPassengerListDTOs($passengers);
        }
        return $dtos;
    }

    public function getDataSrcUrl() {
        return null;
    }

    public function getMenuIdentifier() {
        return 'tixiapi_passengers_get';
    }
}