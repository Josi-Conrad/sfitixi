<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.05.14
 * Time: 18:36
 */

namespace Tixi\ApiBundle\Shared\DataGrid\GridControllers\Dispo;


use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanListDTO;
use Tixi\ApiBundle\Shared\DataGrid\DataGridAbstractController;
use Tixi\ApiBundle\Shared\DataGrid\DataGridHandler;
use Tixi\ApiBundle\Shared\DataGrid\Tile\DataGridCustomControlTile;
use Tixi\ApiBundle\Tile\Core\LinkButtonTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonDividerTile;
use Tixi\ApiBundle\Tile\Core\SelectionButtonTile;
use Tixi\ApiBundle\Tile\Core\TextLinkSelectionTile;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericEntityFilter;

class MonthlyPlanDataGridController extends DataGridAbstractController{
    /**
     * @return mixed
     */
    public function getGridIdentifier()
    {
        return 'monthlyplans';
    }

    /**
     * @return mixed
     */
    public function createCustomControlTile()
    {
        $customControlTile = new DataGridCustomControlTile();
        $selectionButton = $customControlTile->add(new SelectionButtonTile($this->getGridIdentifier() . '_selection', 'button.with.selection'));
        $selectionButton->add(new TextLinkSelectionTile('show', $this->generateUrl('tixiapi_dispo_monthlyplan_workingdays_get', array('workingMonthId' => DataGridHandler::$dataGirdReplaceIdentifier)), 'button.show', true));
        $selectionButton->add(new SelectionButtonDividerTile()
        );
        $customControlTile->add(new LinkButtonTile($this->getGridIdentifier() . '_new', $this->generateUrl('tixiapi_dispo_productionplan_new'), 'monthlyplan.button.new', LinkButtonTile::$primaryType));
        return $customControlTile;
    }

    /**
     * @return mixed
     */
    public function getDblClickPath()
    {
        return $this->generateUrl('tixiapi_dispo_monthlyplan_workingdays_get', array('workingMonthId' => DataGridHandler::$dataGirdReplaceIdentifier));
    }

    /**
     * @return mixed
     */
    public function getReferenceDTO()
    {
        $referenceDTO = null;
        if (!$this->isInEmbeddedState()) {
            $referenceDTO = MonthlyPlanListDTO::createReferenceDTOByWorkingMonthId(DataGridHandler::$dataGirdReplaceIdentifier);
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
        $workingMonths = $this->getEntitiesByFgeaFilter($filter);
        $dtos = array();
        if (!$this->isInEmbeddedState()) {
            $dtos = $assembler->workingMonthsToListDTOs($workingMonths);
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