<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\PersonCategoryListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;

/**
 * Class PersonCategoryDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management
 */
class PersonCategoryDataGridController extends DataGridAbstractController {
    /**
     * @return mixed|string
     */
    public function getGridIdentifier() {
        return 'personcategory';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier() . '_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_personcategory_edit', array('personCategoryId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_personcategory_delete', array('personCategoryId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier() . '_new', $this->generateUrl('tixiapi_management_personcategory_new'), 'personcategory.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_management_personcategory_edit', array('personCategoryId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|PersonCategoryListDTO
     */
    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new PersonCategoryListDTO();
        }
        return null;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerPersonCategory');
        $personCategorys = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->PersonCategorysToPersonCategoryListDTOs($personCategorys);
        }
        return $dtos;
    }

    /**
     * @return mixed|null
     */
    public function getDataSrcUrl() {
        return null;
    }
}