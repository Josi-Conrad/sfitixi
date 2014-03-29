<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\POI
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\POIRepositoryDoctrine")
 * @ORM\Table(name="poi")
 */
class POI {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="pois")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\ManyToMany(targetEntity="POIKeyword", inversedBy="pois")
     * @ORM\JoinTable(name="poi_to_keywords")
     **/
    protected $keywords;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * Department name like "Dialyse"
     * @ORM\Column(type="string", length=50)
     */
    protected $department;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $telephone;

    /**
     * Comments used on the DriveMission
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $details;

    private function __construct() {
        $this->keywords = new ArrayCollection();
    }

    /**
     * @param $name
     * @param $department
     * @param Address $address
     * @param null $telephone
     * @param null $comment
     * @param null $memo
     * @param null $details
     * @return POI
     */
    public static function registerPoi($name, $department, Address $address,
                                       $telephone = null, $comment = null, $memo = null, $details = null) {
        $poi = new POI();

        $poi->setName($name);
        $poi->setDepartment($department);
        $poi->assignAddress($address);

        if (!empty($telephone)) {
            $poi->setTelephone($telephone);
        }
        if (!empty($comment)) {
            $poi->setComment($comment);
        }
        if (!empty($memo)) {
            $poi->setMemo($memo);
        }
        if (!empty($details)) {
            $poi->setDetails($details);
        }

        $poi->activate();

        return $poi;
    }

    /**
     * @param null $name
     * @param null $department
     * @param Address $address
     * @param null $telephone
     * @param null $comment
     * @param null $memo
     * @param null $details
     */
    public function updateBasicData($name = null, $department = null, Address $address = null,
                                    $telephone = null, $comment = null, $memo = null, $details = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($department)) {
            $this->setDepartment($department);
        }
        if (!empty($address)) {
            $this->setAddress($address);
        }
        if (!empty($telephone)) {
            $this->setTelephone($telephone);
        }
        if (!empty($comment)) {
            $this->setComment($comment);
        }
        if (!empty($memo)) {
            $this->setMemo($memo);
        }
        if (!empty($details)) {
            $this->setDetails($details);
        }
    }

    /**
     * @param POI $poi
     */
    public static function removePoi(POI $poi) {
        $poi->getAddress()->removePoi($poi);
    }

    /**
     * @param Address $address
     */
    public function assignAddress(Address $address) {
        $this->address = $address;
        $address->assignPoi($this);
    }

    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param POIKeyword $keyword
     */
    public function assignKeyword(POIKeyword $keyword) {
        $keyword->assignPOI($this);
        $this->keywords->add($keyword);
    }

    /**
     * @param POIKeyword $keyword
     */
    public function unsignKeyword(POIKeyword $keyword) {
        $keyword->unsignPOI($this);
        $this->keywords->removeElement($keyword);
    }

    /**
     * @param POIKeyword $keyword
     */
    public function removeKeyword(POIKeyword $keyword) {
        $this->keywords->removeElement($keyword);
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * @param mixed $department
     */
    public function setDepartment($department) {
        $this->department = $department;
    }

    /**
     * @return mixed
     */
    public function getDepartment() {
        return $this->department;
    }

    /**
     * @param mixed $details
     */
    public function setDetails($details) {
        $this->details = $details;
    }

    /**
     * @return mixed
     */
    public function getDetails() {
        return $this->details;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * @return ArrayCollection
     */
    public function getKeywords() {
        return $this->keywords;
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
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone() {
        return $this->telephone;
    }
}
