<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\AbsentEmbeddedListDTO;
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
 * Class PassengerAbsentDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers
 */
class PassengerAbsentDataGridController extends DataGridAbstractController{
    /**
     * @return mixed|string
     */
    public function getGridIdentifier()
    {
        return 'absents';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_passenger_absent_get',array('passengerId'=>$this->routeProperties['passengerId'],'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.show',true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_passenger_absent_edit',array('passengerId'=>$this->routeProperties['passengerId'],'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_passenger_absent_delete',array('passengerId'=>$this->routeProperties['passengerId'],'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_passenger_absent_new',array('passengerId'=>$this->routeProperties['passengerId'])), 'absent.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_passenger_absent_get',array('passengerId'=>$this->routeProperties['passengerId'], 'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|null|AbsentEmbeddedListDTO
     */
    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = AbsentEmbeddedListDTO::createReferenceDTOByPersonId($this->routeProperties['passengerId']);
        }
        return $referenceDTO;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblerabsent');
        $absents = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->absentsToAbsentEmbeddedListDTOs($absents);
        }
        return $dtos;
    }

    /**
     * @return mixed|string
     */
    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_passenger_absents_get',array('passengerId'=>$this->routeProperties['passengerId']));
    }

}