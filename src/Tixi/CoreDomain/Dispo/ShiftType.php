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

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param $start
     * @param $end
     * @return ShiftType
     */
    public static function registerShiftType($name, $start, $end) {
        $shiftType = new ShiftType();
        $shiftType->setName($name);
        $shiftType->setStart($start);
        $shiftType->setEnd($end);
        return $shiftType;
    }

    /**
     * @param null $name
     * @param null $start
     * @param null $end
     */
    public function updateShiftTypeData($name = null, $start = null, $end = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($start)) {
            $this->setStart($start);
        }
        if (!empty($end)) {
            $this->setEnd($end);
        }
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isResponsibleForTime(\DateTime $dateTime) {
        //TODO:
        return true;
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

} 