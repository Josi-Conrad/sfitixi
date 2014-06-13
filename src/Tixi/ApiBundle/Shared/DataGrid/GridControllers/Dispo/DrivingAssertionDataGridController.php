<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.05.14
 * Time: 10:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;


use Tixi\ApiBundle\Interfaces\AbsentEmbeddedListDTO;
use Tixi\ApiBundle\Interfaces\Dispo\DrivingAssertionEmbeddedListDTO;
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
 * Class DrivingAssertionDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo
 */
class DrivingAssertionDataGridController extends DataGridAbstractController{
    /**
     * @return mixed|string
     */
    public function getGridIdentifier()
    {
        return 'drivingassertions';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_driver_drivingassertion_delete',array('driverId'=>$this->routeProperties['driverId'],'drivingAssertionId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath()
    {
        return null;
    }

    /**
     * @return mixed|null|AbsentEmbeddedListDTO
     */
    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if(!$this->isInEmbeddedState()) {

        }else {
            $referenceDTO = DrivingAssertionEmbeddedListDTO::createReferenceDTOByDriverId($this->routeProperties['driverId']);
        }
        return $referenceDTO;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblerdrivingassertion');
        $drivingAssertions = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {

        }else {
            $dtos = $assembler->drivingAssertionToDrivngAssertionEmbeddedListDTOs($drivingAssertions);
        }
        return $dtos;
    }

    /**
     * @return mixed|string
     */
    public function getDataSrcUrl()
    {
        return $this->generateUrl('tixiapi_driver_drivingassertions_get',array('driverId'=>$this->routeProperties['driverId']));
    }
} 