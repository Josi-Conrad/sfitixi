<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\VehicleCategoryAssembler;
use Tixi\ApiBundle\Interfaces\Management\VehicleCategoryListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class VehicleCategoryDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'vehiclecategories';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_vehiclecategory_edit',array('vehicleCategoryId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_vehiclecategory_delete',array('vehicleCategoryId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $linkButton = $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_management_vehiclecategory_new'),'vehiclecategory.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_management_vehiclecategory_edit',array('vehicleCategoryId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        if(!$this->isInEmbeddedState()) {
            return new VehicleCategoryListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        /** @var VehicleCategoryAssembler $assembler */
        $assembler = $this->container->get('tixi_api.assemblervehiclecategory');
        $vehicleCategories = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {
            $dtos = $assembler->vehicleCategoriesToVehicleCategoryListDTOs($vehicleCategories);
        }
        return $dtos;
    }

    public function getDataSrcUrl()
    {
        return null;
    }
}