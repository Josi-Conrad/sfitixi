<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Absent
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\AbsentRepositoryDoctrine")
 * @ORM\Table(name="absent")
 */
class Absent {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $subject;

    /**
     * @ORM\Column(type="date")
     */
    protected $startDate;

    /**
     * @ORM\Column(type="date")
     */
    protected $endDate;

    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="absent")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    private function __construct() {
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
    }

    public static function registerAbsent($subject, $startDate, $endDate) {
        $absent = new Absent();

        $absent->setSubject($subject);
        $absent->setstartDate($startDate);
        $absent->setendDate($endDate);

        return $absent;
    }

    public function updateBasicData($subject=null, $startDate=null, $endDate=null) {
        if(!is_null($subject)) {$this->setSubject($subject);}
        if(!is_null($startDate)) {$this->setstartDate($startDate);}
        if(!is_null($endDate)) {$this->setendDate($endDate);}
    }

    public function setStartDate($startDate) {
        $this->startDate = new \DateTime($startDate);
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function setEndDate($endDate) {
        $this->endDate = new \DateTime($endDate);
    }

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
     * @return
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $person
     */
    public function setPerson($person) {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getPerson() {
        return $this->person;
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

}
