<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.02.14
 * Time: 12:17
 */

namespace Tixi\CoreDomain;

use Tixi\CoreDomain\Shared\CommonBaseRepository;

/**
 * Interface BankHolidayRepository
 * @package Tixi\CoreDomain
 */
interface BankHolidayRepository extends CommonBaseRepository {
    /**
     * @param BankHoliday $bankHoliday
     * @return mixed
     */
    public function store(BankHoliday $bankHoliday);

    /**
     * @param BankHoliday $bankHoliday
     * @return mixed
     */
    public function remove(BankHoliday $bankHoliday);
} 