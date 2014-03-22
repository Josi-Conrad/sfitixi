<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 03.03.14
 * Time: 09:38
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\Entity;

/**
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ServicePlanRepositoryDoctrine")
 * @ORM\Table(name="serviceplan")
 */
class ServicePlan implements Entity{

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
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    protected $cost;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    private function __construct() {

    }

    /**
     * @param $startDate
     * @param $endDate
     * @return ServicePlan
     */
    public static function registerServicePlan($startDate, $endDate) {
        $servicePlan = new ServicePlan();
        $servicePlan->setStartDate($startDate);
        $servicePlan->setEndDate($endDate);
        return $servicePlan;
    }

    public function updateBasicData($startDate=null, $endDate=null) {
        if(!is_null($startDate)) {$this->startDate=$startDate;}
        if(!is_null($endDate)) {$this->endDate=$endDate;}
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost) {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getCost() {
        return $this->cost;
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
     * @param mixed $vehicle
     */
    public function setVehicle($vehicle) {
        $this->vehicle = $vehicle;
    }

    /**
     * @return mixed
     */
    public function getVehicle() {
        return $this->vehicle;
    }


} 