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
use Tixi\CoreDomain\Shared\Entity;

/**
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ServicePlanRepositoryDoctrine")
 * @ORM\Table(name="serviceplan")
 */
class ServicePlan extends CommonBaseEntity implements Entity {

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
     * @ORM\Column(type="datetime")
     */
    protected $startDate;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $endDate;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    protected  function __construct() {
        parent::__construct();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param null $memo
     * @return ServicePlan
     */
    public static function registerServicePlan($startDate, $endDate, $memo = null) {
        $servicePlan = new ServicePlan();
        $servicePlan->setStartDate($startDate);
        $servicePlan->setEndDate($endDate);
        if (!empty($memo)) {
            $servicePlan->setMemo($memo);
        }
        return $servicePlan;
    }

    public function updateBasicData($startDate = null, $endDate = null, $memo = null) {
        if (!empty($startDate)) {
            $this->setStartDate($startDate);
        }
        if (!empty($endDate)) {
            $this->setEndDate($endDate);
        }
        if (!empty($memo)) {
            $this->setMemo($memo);
        }
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
     * @param mixed $endDate
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEndDate() {
        return $this->endDate;
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
     * @param mixed $startDate
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle() {
        return $this->vehicle;
    }


} 