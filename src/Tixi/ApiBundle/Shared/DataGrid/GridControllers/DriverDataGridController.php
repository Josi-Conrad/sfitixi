<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\DriverListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class DriverDataGridController extends DataGridAbstractController {

    public function getGridIdentifier() {
        return 'drivers';
    }

    public function getGridDisplayTitel() {
        return 'Fahrer';
    }

    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('Mit Auswahl'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_driver_editbasic', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Editieren', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_driver_absent_new', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Neue Abwesenheit', true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_driver_editbasic', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Neuer Dauereinsatz', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_driver_editbasic', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'Löschen', true));
        $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_driver_new'), 'Neuer Fahrer', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_driver_get', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new DriverListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerdriver');
        $drivers = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->driversToDriverListDTOs($drivers);
        }
        return $dtos;
    }

    public function getDataSrcUrl() {
        return null;
    }

    public function getMenuIdentifier() {
        return 'tixiapi_drivers_get';
    }
}