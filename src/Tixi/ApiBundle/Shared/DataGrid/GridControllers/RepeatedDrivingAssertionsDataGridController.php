<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 11:28
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionEmbeddedListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class RepeatedDrivingAssertionsDataGridController extends DataGridAbstractController {

    public function getGridIdentifier()
    {
        return 'repeatedassertions';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_driver_repeatedassertionplan_edit',array('driverId'=>$this->routeProperties['driverId'],'assertionPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.show',true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_driver_repeatedassertionplan_edit',array('driverId'=>$this->routeProperties['driverId'],'assertionPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_driver_repeatedassertionplan_delete',array('driverId'=>$this->routeProperties['driverId'],'assertionPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_driver_repeatedassertionplan_new',array('driverId'=>$this->routeProperties['driverId'])), 'repeateddrivingmission.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_driver_repeatedassertionplan_edit',array('driverId'=>$this->routeProperties['driverId'],'assertionPlanId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = RepeatedDrivingAssertionEmbeddedListDTO::createReferenceDTOByDriverId($this->routeProperties['driverId']);
        }
        return $referenceDTO;
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.repeateddrivingassertionplanassembler');
        $assertionPlans = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->assertionPlansToEmbeddedListDTOs($assertionPlans);
        }
        return $dtos;

    }

    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_driver_repeatedassertionplans_get',array('driverId'=>$this->routeProperties['driverId']));

    }
}