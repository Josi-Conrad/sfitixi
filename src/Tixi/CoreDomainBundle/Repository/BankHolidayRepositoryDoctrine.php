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
use Tixi\CoreDomain\Dispo\WorkingDay;

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

    /**
     * @param WorkingDay $workingDay
     * @return bool
     */
    public function checkIfWorkingDayIsBankHoliday(WorkingDay $workingDay) {
        $day = $workingDay->getDate();
        $qb = parent::createQueryBuilder('e');
        $qb->where('e.date = :day')
            ->andWhere('e.isDeleted = 0')
            ->setParameter('day', $day->format('Y-m-d'));
        if ($qb->getQuery()->getResult()) {
            return true;
        } else {
            return false;
        }
    }

}