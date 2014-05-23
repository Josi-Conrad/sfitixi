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
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\POI
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\POIRepositoryDoctrine")
 * @ORM\Table(name="poi")
 */
class POI extends CommonBaseEntity {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * old Id from data integration
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $pin;

    /**
     * @ORM\ManyToOne(targetEntity="Address", inversedBy="pois")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     */
    protected $address;

    /**
     * @ORM\ManyToMany(targetEntity="POIKeyword", inversedBy="pois")
     * @ORM\JoinTable(name="poi_to_keyword",
     *      joinColumns={@ORM\JoinColumn(name="poi_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="keyword_id", referencedColumnName="id")})
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
     * @ORM\Column(type="string", length=50, nullable=true)
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
    protected $details;

    protected  function __construct() {
        $this->keywords = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $name
     * @param $department
     * @param Address $address
     * @param null $telephone
     * @param null $comment
     * @param null $details
     * @return POI
     */
    public static function registerPoi($name, Address $address, $department = null,
                                       $telephone = null, $comment = null, $details = null) {
        $poi = new POI();

        if (!empty($name)) {
            $poi->setName($name);
        }
        if (!empty($address)) {
            $poi->assignAddress($address);
        }

        $poi->setDepartment($department);
        $poi->setTelephone($telephone);
        $poi->setComment($comment);
        $poi->setDetails($details);

        $poi->activate();

        return $poi;
    }

    /**
     * @param null $name
     * @param null $department
     * @param Address $address
     * @param null $telephone
     * @param null $comment
     * @param null $details
     */
    public function updatePOIData($name = null, Address $address = null, $department = null,
                                    $telephone = null, $comment = null, $details = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($address)) {
            $this->assignAddress($address);
        }
        $this->setDepartment($department);
        $this->setTelephone($telephone);
        $this->setComment($comment);
        $this->setDetails($details);
        $this->updateModifiedDate();
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
        $this->keywords->add($keyword);
    }

    /**
     * @param POIKeyword $keyword
     */
    public function removeKeyword(POIKeyword $keyword) {
        $this->keywords->removeElement($keyword);
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
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

    /**
     * @return string
     */
    public function getKeywordsAsString() {
        return self::constructKeywordsString($this->getKeywords());
    }

    /**
     * @param POIKeyword[] $keywords
     * @return string
     */
    public static function constructKeywordsString($keywords) {
        $string = '';
        foreach ($keywords as $key => $keyword) {
            if ($key !== 0) {
                $string .= ', ';
            }
            $string .= $keyword->getName();
        }
        return $string;
    }

}
