<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\PoiKeywordAssembler;
use Tixi\ApiBundle\Interfaces\Management\PoiKeywordListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class PoiKeywordDataGridController extends DataGridAbstractController{

    public function getGridIdentifier()
    {
        return 'poikeywords';
    }

    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier().'_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_poikeyword_edit',array('poiKeywordId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.edit',true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_poikeyword_delete',array('poiKeywordId'=>DataGridHandler::$dataGirdReplaceIdentifier)),'button.delete',true));
        $linkButton = $customControlTile->add(new LinkButtonTile($this->getGridIdentifier().'_new', $this->generateUrl('tixiapi_management_poikeyword_new'),'poikeyword.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_management_poikeyword_edit',array('poiKeywordId'=>DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO()
    {
        if(!$this->isInEmbeddedState()) {
            return new PoiKeywordListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        /** @var PoiKeywordAssembler $assembler */
        $assembler = $this->container->get('tixi_api.assemblerpoikeyword');
        $poiKeywords = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if(!$this->isInEmbeddedState()) {
            $dtos = $assembler->poiKeywordsToPoiKeywordListDTOs($poiKeywords);
        }
        return $dtos;
    }

    public function getDataSrcUrl()
    {
        return null;
    }
}