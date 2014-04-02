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
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class VehicleDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'vehicles';
    }

    public function getGridDisplayTitel()
    {
        return 'Fahrzeuge';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('Mit Auswahl'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_vehicle_editbasic',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'Editieren',true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_serviceplan_new',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'Neuer Serviceplan',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_vehicle_editbasic',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'Löschen',true));
        $linkButton = $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_vehicle_new'),'Neues Fahrzeug', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }


    public function getReferenceDTO()
    {
        if(!$this->isInEmbeddedState()) {
            return new VehicleListDTO();
        }
    }

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

    public function getDataSrcUrl()
    {
        return null;
    }

    public function getMenuIdentifier()
    {
        return 'tixiapi_vehicles_get';
    }
}