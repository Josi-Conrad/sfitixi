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
use Tixi\ApiBundle\Tile\Core\TextLinkListDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class PassengerDataGridController extends DataGridAbstractController {

    public function getGridIdentifier() {
        return 'passengers';
    }

    public function getGridDisplayTitel() {
        return 'passenger.list.name';
    }

    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('button.with.selection'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_get', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.show', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_edit', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_absent_new', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'absent.button.new', true));
//        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_edit', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'drivingorder.button.new', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListDeleteTile($this->generateUrl('tixiapi_passenger_delete', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_passenger_new'), 'passenger.button.new', LinkButtonTile::$primaryType));
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