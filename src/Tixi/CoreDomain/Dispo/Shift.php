<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;

/**
 * Tixi\CoreDomain\Dispo\Shift
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\ShiftRepositoryDoctrine")
 * @ORM\Table(name="shift")
 */
class Shift {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="ShiftType", inversedBy="shifts")
     * @ORM\JoinColumn(name="shift_type_id", referencedColumnName="id")
     */
    protected $shiftType;
    /**
     * @ORM\OneToMany(targetEntity="DrivingPool", mappedBy="shift")
     * @ORM\JoinColumn(name="driving_pool_id", referencedColumnName="id")
     */
    protected $drivingPools;
    /**
     * @ORM\ManyToOne(targetEntity="WorkingDay", inversedBy="shifts")
     * @ORM\JoinColumn(name="working_day_id", referencedColumnName="id")
     */
    protected $workingDay;

    protected $amountOfDrivers;

    public function __construct(ShiftType $shiftType) {
        $this->shiftType = $shiftType;

        $this->drivingPools = new ArrayCollection();
    }

    protected function assignDriver(Driver $driver) {
        $this->drivingPools = new DrivingPool($driver, $this);
    }

    protected function amountOfDriversNeede() {
        return $this->amountOfDrivers - $this->count($this->drivingPools);
    }


}

//    public function getAmountOfDrivers() {
//        return count($this->drivingPools);
//    }

