<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 07.06.14
 * Time: 01:03
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;


use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderEmbeddedListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class DrivingOrderDataGridController extends DataGridAbstractController{

    /**
     * @return mixed
     */
    public function getGridIdentifier()
    {
        return 'drivingorders';
    }

    /**
     * @return mixed
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_passenger_drivingorder_edit',array('passengerId'=>$this->routeProperties['passengerId'],'drivingOrderId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_passenger_drivingorder_delete',array('passengerId'=>$this->routeProperties['passengerId'],'drivingOrderId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_passenger_drivingorder_new',array('passengerId'=>$this->routeProperties['passengerId'])), 'drivingorder.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_passenger_drivingorder_edit',array('passengerId'=>$this->routeProperties['passengerId'],'drivingOrderId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed
     */
    public function getReferenceDTO()
    {
        {
            $referenceDTO = null;
            if(!$this->isInEmbeddedState()) {

            }else {
                $referenceDTO = DrivingOrderEmbeddedListDTO::createReferenceDTOByDriverId($this->routeProperties['passengerId']);
            }
            return $referenceDTO;
        }
    }

    /**
     * @param GenericEntityFilter $filter
     * @return mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        /** @var DrivingOrderAssembler $assembler */
        $assembler = $this->container->get('tixi_api.assemblerdrivingorder');
        $drivingOrders = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->drivingOrdersToDrivingOrderEmbeddedListDTOs($drivingOrders);
        }
        return $dtos;
    }

    /**
     * @return mixed
     */
    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_passenger_drivingorders_get',array('passengerId'=>$this->routeProperties['passengerId']));
    }
}