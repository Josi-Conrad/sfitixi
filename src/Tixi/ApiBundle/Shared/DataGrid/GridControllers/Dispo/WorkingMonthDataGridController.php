<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;


use Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthListDTO;
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
 * Class WorkingMonthDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo
 */
class WorkingMonthDataGridController extends DataGridAbstractController {
    /**
     * @return mixed|string
     */
    public function getGridIdentifier() {
        return 'workingmonth';
    }

    /**
     * @return mixed|DataGridCustomControlTile
     */
    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier() . '_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_dispo_workingmonth_edit', array('workingMonthId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new SelectionButtonDividerTile());
//        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_dispo_workingmonth_delete', array('bankHolidayId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier() . '_new', $this->generateUrl('tixiapi_dispo_workingmonth_new'), 'workingmonth.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed|string
     */
    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_dispo_workingmonth_edit', array('workingMonthId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed|null|WorkingMonthListDTO
     */
    public function getReferenceDTO() {
        $referenceDTO = null;
        if (!$this->isInEmbeddedState()) {
            $referenceDTO = WorkingMonthListDTO::createReferenceDTOByWorkingMonthId(DataGridHandler::$dataGirdReplaceIdentifier);
        } else {
        }
        return $referenceDTO;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return array|mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerWorkingMonth');
        $workingMonths = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->workingMonthsToListDTOs($workingMonths);
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