<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 21:56
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tixi\ApiBundle\Interfaces\VehicleListDTO;
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
 * Class VehicleDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers
 */
class VehicleDataGridController extends DataGridAbstractController{
    /**
     * @return mixed|string
     */
    public function getGridIdentifier()
    {
        return 'vehicles';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.show',true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_vehicle_edit',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new TextLinkSelectionTile('new_serviceplan', $this->generateUrl('tixiapi_serviceplan_new',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'serviceplan.button.new',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_vehicle_delete',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_vehicle_new'),'vehicle.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|VehicleListDTO
     */
    public function getReferenceDTO()
    {
        if(!$this->isInEmbeddedState()) {
            return new VehicleListDTO();
        }
        return null;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblervehicle');
        $vehicles = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {
            $dtos = $assembler->vehiclesToVehicleListDTOs($vehicles);
        }
        return $dtos;
    }

    /**
     * @return mixed|null
     */
    public function getDataSrcUrl()
    {
        return null;
    }
}