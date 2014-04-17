<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 03.03.14
 * Time: 09:41
 */

namespace Tixi\CoreDomainBundle\Repository;

use Tixi\CoreDomain\BankHoliday;
use Tixi\CoreDomain\BankHolidayRepository;

/**
 * Class BankHolidayRepositoryDoctrine
 * @package Tixi\CoreDomainBundle\Repository
 */
class BankHolidayRepositoryDoctrine extends CommonBaseRepositoryDoctrine implements BankHolidayRepository {
    /**
     * @param BankHoliday $bankHoliday
     * @return mixed|void
     */
    public function store(BankHoliday $bankHoliday) {
        $this->getEntityManager()->persist($bankHoliday);
    }

    /**
     * @param BankHoliday $bankHoliday
     * @return mixed|void
     */
    public function remove(BankHoliday $bankHoliday) {
        $this->getEntityManager()->remove($bankHoliday);
    }

}