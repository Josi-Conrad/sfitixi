<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class VehicleCategoryDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'vehicletypesy';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_vehicle_get',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.show',true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_vehicle_edit',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_vehicle_delete',array('vehicleId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $linkButton = $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_vehicle_new'),'vehicle.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        // TODO: Implement getDblClickPath() method.
    }

    public function getReferenceDTO()
    {
        // TODO: Implement getReferenceDTO() method.
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        // TODO: Implement constructDtosFromFgeaFilter() method.
    }

    public function getDataSrcUrl()
    {
        // TODO: Implement getDataSrcUrl() method.
    }
}