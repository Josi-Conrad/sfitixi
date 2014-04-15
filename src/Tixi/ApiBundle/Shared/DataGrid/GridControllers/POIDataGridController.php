<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 11:49
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers;


use Tixi\ApiBundle\Interfaces\POIListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class POIDataGridController extends DataGridAbstractController {

    public function getGridIdentifier() {
        return 'pois';
    }

    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_poi_get', array('poiId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.show', true));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_poi_edit', array('poiId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_poi_delete', array('poiId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_poi_new'), 'poi.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_poi_get', array('poiId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new POIListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerpoi');
        $pois = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->poisToPOIListDTOs($pois);
        }
        return $dtos;
    }

    public function getDataSrcUrl() {
        return null;
    }
}