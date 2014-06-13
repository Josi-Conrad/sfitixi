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
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * Class PassengerDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers
 */
class PassengerDataGridController extends DataGridAbstractController {
    /**
     * @return mixed|string
     */
    public function getGridIdentifier() {
        return 'passengers';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_passenger_get', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.show', true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_passenger_edit', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new TextLinkSelectionTile('new_absent', $this->generateUrl('tixiapi_passenger_absent_new', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'absent.button.new', true));
        $selectionButton->add(new TextLinkSelectionTile('new_order', $this->generateUrl('tixiapi_passenger_drivingorder_new', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'drivingorder.button.new', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_passenger_delete', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_passenger_new'), 'passenger.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_passenger_get', array('passengerId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|PassengerListDTO
     */
    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new PassengerListDTO();
        }
        return null;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerpassenger');
        $passengers = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->passengersToPassengerListDTOs($passengers);
        }
        return $dtos;
    }

    /**
     * @return mixed|null
     */
    public function getDataSrcUrl() {
        return null;
    }
}