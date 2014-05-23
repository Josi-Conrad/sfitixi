<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\ZoneAssembler;
use Tixi\ApiBundle\Interfaces\Management\ZoneListDTO;
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
 * Class ZoneDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management
 */
class ZoneDataGridController extends DataGridAbstractController {
    /**
     * @return mixed|string
     */
    public function getGridIdentifier()
    {
        return 'zones';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_zone_edit',array('zoneId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_zone_delete',array('zoneId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_management_zone_new'),'zone.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_management_zone_edit',array('zoneId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|ZoneListDTO
     */
    public function getReferenceDTO()
    {
        if(!$this->isInEmbeddedState()) {
            return new ZoneListDTO();
        }
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        /** @var ZoneAssembler $assembler */
        $assembler = $this->container->get('tixi_api.assemblerzone');
        $zones = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {
            $dtos = $assembler->zonesToZoneListDTOs($zones);
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