<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\ShiftTypeListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;

class ShiftTypeDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'shifttype';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_shifttype_edit',array('shiftTypeId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        //$selectionButton->add(new SelectionButtonDividerTile());
        //$selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_shifttype_delete',array('shiftTypeId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        //$customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_management_shifttype_new'),'shifttype.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_management_shifttype_edit',array('shiftTypeId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        if (!$this->isInEmbeddedState()) {
            return new ShiftTypeListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblerShiftType');
        $shiftTypes = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->ShiftTypesToShiftTypeListDTOs($shiftTypes);
        }
        return $dtos;
    }

    public function getDataSrcUrl()
    {
        return null;
    }
}