<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.04.14
 * Time: 13:07
 */

namespace Tixi\ApiBundle\Tile\CustomFormView;


use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

/**
 * Class ServicePlanRegisterFormViewTile
 * @package Tixi\ApiBundle\Tile\CustomFormView
 */
class ServicePlanRegisterFormViewTile extends AbstractFormViewTile{

    /**
     * @var \Tixi\ApiBundle\Helper\DateTimeService
     */
    protected $dateTimeService;

    /**
     * @param DateTimeService $dateTimeService
     * @param $formViewId
     * @param $dto
     * @param bool $editPath
     * @param bool $isStandalone
     */
    public function __construct(DateTimeService $dateTimeService, $formViewId, $dto, $editPath, $isStandalone=false) {
        $this->dateTimeService = $dateTimeService;
        parent::__construct($formViewId, $dto, $editPath, $isStandalone);
    }

    /**
     * @return mixed|void
     */
    public function createFormRows()
    {
        $this->basicFormRows[] = new FormRowView('subject','serviceplan.field.subject',$this->dto->subject);
        $this->basicFormRows[] = new FormRowView('startDate','serviceplan.field.start',
            $this->dateTimeService->convertDateTimeToDateTimeString($this->dto->start));
        $this->basicFormRows[] = new FormRowView('endDate','serviceplan.field.end',
            $this->dateTimeService->convertDateTimeToDateTimeString($this->dto->end));
        $this->basicFormRows[] = new FormRowView('memo','serviceplan.field.memo',$this->dto->memo);
    }
}