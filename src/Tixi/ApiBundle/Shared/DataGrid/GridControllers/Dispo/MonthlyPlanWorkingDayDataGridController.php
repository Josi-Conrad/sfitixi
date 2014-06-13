<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.05.14
 * Time: 18:36
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanWorkingDayListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

/**
 * Class MonthlyPlanWorkingDayDataGridController
 * @package Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo
 */
class MonthlyPlanWorkingDayDataGridController extends DataGridAbstractController{

    /**
     * @return mixed
     */
    public function getGridIdentifier()
    {
        return 'monthlyplans_workingdays';
    }

    /**
     * @return mixed
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier() . '_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('edit', $this->generateUrl('tixiapi_dispo_monthlyplan_edit', array('workingMonthId'=>$this->routeProperties['workingMonthId'],'workingDayId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.edit', true));
        $selectionButton->add(new SelectionButtonDividerTile()
        );
        return $customControlTile;
    }

    /**
     * @return mixed
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_dispo_monthlyplan_edit', array('workingMonthId'=>$this->routeProperties['workingMonthId'],'workingDayId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed
     */
    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if (!$this->isInEmbeddedState()) {
            $referenceDTO = MonthlyPlanWorkingDayListDTO::createReferenceDTOByWorkingDayId($this->routeProperties['workingMonthId']);
        } else {
        }
        return $referenceDTO;
    }

    /**
     * @param GenericEntityFilter $filter
     * @return mixed
     */
    public function constructDtosFromFgeaFilter(GenericEntityFilter $filter)
    {
        $assembler = $this->container->get('tixi_api.assemblermonthlyplan');
        $workingDays = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->workingMonthsTooWorkingDayListDTOs($workingDays);
        }
        return $dtos;
    }

    /**
     * @return mixed
     */
    public function getDataSrcUrl()
    {
        return null;
    }
}