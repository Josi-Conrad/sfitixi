<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 09:38
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ServicePlanRepositoryDoctrine")
 * @ORM\Table(name="serviceplan")
 */
class ServicePlan extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Vehicle", inversedBy="servicePlans")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    protected $vehicle;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $start;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $end;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $subject;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $start
     * @param $end
     * @param null $subject
     * @param null $memo
     * @return ServicePlan
     */
    public static function registerServicePlan($start, $end, $subject = null, $memo = null) {
        $servicePlan = new ServicePlan();
        $servicePlan->setStart($start);
        $servicePlan->setEnd($end);
        $servicePlan->setSubject($subject);
        $servicePlan->setMemo($memo);
        return $servicePlan;
    }

    /**
     * @param null $start
     * @param null $end
     * @param null $subject
     * @param null $memo
     */
    public function updateServicePlanData($start = null, $end = null, $subject = null, $memo = null) {
        if (!empty($start)) {
            $this->setStart($start);
        }
        if (!empty($end)) {
            $this->setEnd($end);
        }
        $this->setSubject($subject);
        $this->setMemo($memo);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function matchDate(\DateTime $date) {
        if ($this->getStart() <= $date || $this->getEnd() >= $date) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $vehicle
     */
    public function assignVehicle($vehicle) {
        $this->vehicle = $vehicle;
    }

    public function removeVehicle() {
        $this->assignVehicle(null);
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end) {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getEnd() {
        return $this->end;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
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
     * @param mixed $start
     */
    public function setStart($start) {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getStart() {
        return $this->start;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle() {
        return $this->vehicle;
    }


} 