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
use Tixi\ApiBundle\Tile\Core\TextLinkListTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class PassengerAbsentDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'absents';
    }

    public function getGridDisplayTitel()
    {
        return 'Abwesenheiten';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile('Mit Auswahl'));
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_absent_editbasic',array('passengerId'=>$this->routeProperties['passengerId'],'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'Editieren',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkListTile($this->generateUrl('tixiapi_passenger_absent_editbasic',array('passengerId'=>$this->routeProperties['passengerId'],'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'Löschen',true));
        $customControlTile->add(new LinkButtonTile($this->generateUrl('tixiapi_passenger_absent_new',array('passengerId'=>$this->routeProperties['passengerId'])), 'Neue Abwesenheit', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_passenger_absent_get',array('passengerId'=>$this->routeProperties['passengerId'], 'absentId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = AbsentEmbeddedListDTO::createReferenceDTOByPersonId($this->routeProperties['passengerId']);
        }
        return $referenceDTO;
    }

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

    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_passenger_absents_get',array('passengerId'=>$this->routeProperties['passengerId']));
    }

    public function getMenuIdentifier()
    {
        return 'tixiapi_passengers_get';
    }
}