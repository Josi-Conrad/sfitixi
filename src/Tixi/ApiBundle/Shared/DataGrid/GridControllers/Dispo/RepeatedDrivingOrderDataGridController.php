<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.06.14
 * Time: 22:45
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;

use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingOrderAssembler;
use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingOrderEmbeddedListDTO;
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
 * Class RepeatedDrivingOrderDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo
 */
class RepeatedDrivingOrderDataGridController extends DataGridAbstractController{

    /**
     * @return mixed
     */
    public function getGridIdentifier()
    {
        return 'repeatedorders';
    }

    /**
     * @return mixed
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_driver_repeatedorderplan_edit',array('passengerId'=>$this->routeProperties['passengerId'],'orderPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_driver_repeatedorderplan_delete',array('passengerId'=>$this->routeProperties['passengerId'],'orderPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_passenger_drivingorder_new',array('passengerId'=>$this->routeProperties['passengerId'])), 'drivingorder.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_driver_repeatedorderplan_edit',array('passengerId'=>$this->routeProperties['passengerId'],'orderPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed
     */
    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = RepeatedDrivingOrderEmbeddedListDTO::createReferenceDTOByPassengerId($this->routeProperties['passengerId']);
        }
        return $referenceDTO;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        /** @var RepeatedDrivingOrderAssembler $assembler */
        $assembler = $this->container->get('tixi_api.assemblerrepeateddrivingorder');
        $orderPlans = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->orderPlansToEmbeddedListDTOs($orderPlans);
        }
        return $dtos;

    }

    /**
     * @return mixed
     */
    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_driver_repeatedorderplans_get',array('passengerId'=>$this->routeProperties['passengerId']));
    }
}