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
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * Class DriverDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers
 */
class DriverDataGridController extends DataGridAbstractController {
    /**
     * @return mixed|string
     */
    public function getGridIdentifier() {
        return 'drivers';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_driver_get', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.show', true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_driver_edit', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new TextLinkSelectionTile('new_absent', $this->generateUrl('tixiapi_driver_absent_new', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'absent.button.new', true));
        $selectionButton->add(new TextLinkSelectionTile('new_repeatedassertion', $this->generateUrl('tixiapi_driver_repeatedassertionplan_new', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'repeateddrivingmission.button.new', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_driver_delete', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_driver_new'), 'driver.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_driver_get', array('driverId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|DriverListDTO
     */
    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new DriverListDTO();
        }
        return null;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerdriver');
        $drivers = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->driversToDriverListDTOs($drivers);
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