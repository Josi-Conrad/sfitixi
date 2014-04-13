<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\ServicePlanEmbeddedListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class ServicePlanDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'serviceplans';
    }

    public function getGridDisplayTitel()
    {
        return 'serviceplan.list.name';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('button.with.selection'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_serviceplan_get',array('vehicleId'=>$this->routeProperties['vehicleId'],'servicePlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.show',true));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_serviceplan_edit',array('vehicleId'=>$this->routeProperties['vehicleId'],'servicePlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListDeleteTile($this->generateUrl('tixiapi_serviceplan_delete',array('vehicleId'=>$this->routeProperties['vehicleId'],'servicePlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_serviceplan_new',array('vehicleId'=>$this->routeProperties['vehicleId'])), 'serviceplan.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_serviceplan_get',array('vehicleId'=>$this->routeProperties['vehicleId'], 'servicePlanId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = ServicePlanEmbeddedListDTO::createReferenceDTOByVehicleId($this->routeProperties['vehicleId']);
        }
        return $referenceDTO;
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblerserviceplan');
        $servicePlans = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->servicePlansToServicePlanEmbeddedListDTOs($servicePlans);
        }
        return $dtos;
    }

    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_serviceplans_get',array('vehicleId'=>$this->routeProperties['vehicleId']));
    }

    public function getMenuIdentifier()
    {
        return 'tixiapi_vehicles_get';
    }
}