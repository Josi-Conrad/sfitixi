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
 * Tixi\CoreDomain\Address
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine")
 * @ORM\Table(name="address")
 */
class Address {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="POI", mappedBy="address")
     * @ORM\JoinColumn(name="poi_id", referencedColumnName="id")
     */
    protected $pois;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $street;
    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $postalCode;
    /**
     * @ORM\Column(type="string", length=30)
     */
    protected $city;
    /**
     * @ORM\Column(type="string", length=30)
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10, nullable=true)
     */
    protected $lat;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10, nullable=true)
     */
    protected $lng;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $type;

    private function __construct() {
        $this->pois = new ArrayCollection();
    }

    /**
     * @param $street
     * @param $postalCode
     * @param $city
     * @param $country
     * @param $name
     * @param null $lat
     * @param null $lng
     * @param null $type
     * @return Address
     */
    public static function registerAddress($street, $postalCode, $city, $country,
                                           $name = null, $lat = null, $lng = null, $type = null) {
        $address = new Address();

        $address->setStreet($street);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);

        if (!empty($name)) {
            $address->setName($name);
        }
        if (!empty($lat)) {
            $address->setLat($lat);
        }
        if (!empty($lng)) {
            $address->setLng($lng);
        }
        if (!empty($type)) {
            $address->setType($type);
        }

        return $address;
    }

    /**
     * @param null $street
     * @param null $postalCode
     * @param null $city
     * @param null $country
     * @param null $name
     * @param null $lat
     * @param null $lng
     * @param null $type
     */
    public function updateAddressBasicData($street = null, $postalCode = null,
                                           $city = null, $country = null,
                                           $name = null, $lat = null, $lng = null, $type = null) {

        if (!empty($street)) {
            $this->setStreet($street);
        }
        if (!empty($postalCode)) {
            $this->setPostalCode($postalCode);
        }
        if (!empty($city)) {
            $this->setCity($city);
        }
        if (!empty($country)) {
            $this->setCountry($country);
        }
        if (!empty($name)) {
            $this->setName($name);
        }
        if (!empty($lat)) {
            $this->setLat($lat);
        }
        if (!empty($lng)) {
            $this->setLng($lng);
        }
        if (!empty($type)) {
            $this->setType($type);
        }
    }

    /**
     * @param POI $poi
     */
    public function assignPoi(POI $poi) {
        $poi->setAddress($this);
        $this->pois->add($poi);
    }

    public function toString() {
        return ($this->getName() + ' ' +
            $this->getStreet() + ' ' +
            $this->getPostalCode() + ' ' +
            $this->getCity() + ' ' +
            $this->getCountry());
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getCountry() {
        return $this->country;
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
     * @param mixed $lat
     */
    public function setLat($lat) {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * @param mixed $lng
     */
    public function setLng($lng) {
        $this->lng = $lng;
    }

    /**
     * @return mixed
     */
    public function getLng() {
        return $this->lng;
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
     * @param mixed $pois
     */
    public function setPois($pois) {
        $this->pois = $pois;
    }

    /**
     * @return mixed
     */
    public function getPois() {
        return $this->pois;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street) {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getStreet() {
        return $this->street;
    }
}
