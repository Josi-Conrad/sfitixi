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
use JsonSchema\Constraints\String;
use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Helper\DateTimeService;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Dispo\ShiftType
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\ShiftTypeRepositoryDoctrine")
 * @ORM\Table(name="shift_type")
 */
class ShiftType extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $start;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $end;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param $start
     * @param $end
     * @param null $memo
     * @return ShiftType
     */
    public static function registerShiftType($name, $start, $end, $memo = null) {
        $shiftType = new ShiftType();
        $shiftType->setName($name);
        $shiftType->setStart($start);
        $shiftType->setEnd($end);
        $shiftType->setMemo($memo);
        return $shiftType;
    }

    /**
     * @param null $name
     * @param null $start
     * @param null $end
     * @param null $memo
     */
    public function updateShiftTypeData($name = null, $start = null, $end = null, $memo = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($start)) {
            $this->setStart($start);
        }
        if (!empty($end)) {
            $this->setEnd($end);
        }
        $this->setMemo($memo);
        parent::updateModifiedDate();
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isResponsibleForTime(\DateTime $dateTime) {
        return DateTimeService::matchTimeBetweenTwoDateTimes($dateTime, $this->start, $this->end);
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start) {
        $this->start = $start;
    }


    /**
     * @return \DateTime
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end) {
        $this->end = $end;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
    }

    /**
     * @return String
     */
    public function __toString() {
        return $this->name;
    }
} 