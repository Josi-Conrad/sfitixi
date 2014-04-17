<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 13:23
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Management;


use Tixi\ApiBundle\Interfaces\Management\BankHolidayListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionDeleteTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;

class BankHolidayDataGridController extends DataGridAbstractController {

    public function getGridIdentifier() {
        return 'bankholiday';
    }

    public function createCustomControlTile() {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier() . '_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_management_bankholiday_edit', array('bankHolidayId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new SelectionButtonDividerTile());
        $selectionButton->add(new TextLinkSelectionDeleteTile('delete', $this->generateUrl('tixiapi_management_bankholiday_delete', array('bankHolidayId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.delete', true));
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier() . '_new', $this->generateUrl('tixiapi_management_bankholiday_new'), 'bankholiday.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    public function getDblClickPath() {
        return $this->generateUrl('tixiapi_management_bankholiday_edit', array('bankHolidayId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    public function getReferenceDTO() {
        if (!$this->isInEmbeddedState()) {
            return new BankHolidayListDTO();
        }
    }

    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter) {
        $assembler = $this->container->get('tixi_api.assemblerBankHoliday');
        $bankHolidays = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->BankHolidaysToBankHolidayListDTOs($bankHolidays);
        }
        return $dtos;
    }

    public function getDataSrcUrl() {
        return null;
    }
}