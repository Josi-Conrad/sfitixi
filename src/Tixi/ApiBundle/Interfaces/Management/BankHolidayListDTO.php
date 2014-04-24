<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 13:19
 */

namespace Tixi\ApiBundle\Interfaces\Management;

use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\GridField;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;
use Tixi\CoreDomain\Shared\GenericEntityFilter\GenericAccessQuery;

/**
 * Class BankHolidayListDTO
 * @package Tixi\ApiBundle\Interfaces
 */
class BankHolidayListDTO implements DataGridSourceClass{
    /**
     * @GridField(rowIdentifier=true, propertyId="BankHoliday.id")
     */
    public $id;
    /**
     * @GridField(propertyId="BankHoliday.isDeleted", restrictive=true)
     */
    public $isDeleted = 'false';
    /**
     * @GridField(propertyId="BankHoliday.name", headerName="bankholiday.field.name", order=1)
     */
    public $name;
    /**
     * @GridField(propertyId="BankHoliday.startDate", headerName="bankholiday.field.start", order=2)
     */
    public $startDate;
    /**
     * @GridField(propertyId="BankHoliday.endDate", headerName="bankholiday.field.end", restrictive=true, comparingOperator=">", order=3)
     */
    public $endDate;

    /**
     * @return GenericAccessQuery
     */
    public function getAccessQuery()
    {
        return new GenericAccessQuery('BankHoliday', 'Tixi\CoreDomain\BankHoliday BankHoliday', 'BankHoliday.id');
    }

    /**
     * @param $bankHolidayId
     * @return BankHolidayListDTO
     */
    public static function createReferenceDTOByBankHolidayId($bankHolidayId) {
        $dto = new BankHolidayListDTO();
        $dto->id = $bankHolidayId;
        $dto->endDate = DateTimeService::getUTCnow();
        return $dto;
    }
}