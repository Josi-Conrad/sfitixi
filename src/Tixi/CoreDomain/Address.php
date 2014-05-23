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
use Tixi\CoreDomainBundle\Util\GeometryService;

/**
 * Tixi\CoreDomain\Address
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine")
 * @ORM\Table(name="address")
 */
class Address extends CommonBaseEntity {

    const SOURCE_MANUAL = 'manual';
    const SOURCE_GOOGLE = 'google';

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
     * @ORM\Column(type="string", length=30)
     */
    protected $source;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $building;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lat;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lng;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $nearestLat;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $nearestLng;

    protected function __construct() {
        parent::__construct();
        $this->pois = new ArrayCollection();
    }

    /**
     * @param $street
     * @param $postalCode
     * @param $city
     * @param $country
     * @param null $name
     * @param null $lat
     * @param null $lng
     * @param string $source
     * @return Address
     */
    public static function registerAddress($street, $postalCode, $city, $country,
                                           $name = null, $lat = null, $lng = null, $source = null) {
        $source = (null !== $source) ? $source : self::SOURCE_MANUAL;
        $address = new Address();

        $address->setStreet($street);
        $address->setPostalCode($postalCode);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setName($name);
        $address->setLat($lat);
        $address->setLng($lng);
        $address->setSource($source);

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
     * @param string $source
     */
    public function updateAddressData($street = null, $postalCode = null,
                                      $city = null, $country = null,
                                      $name = null, $lat = null, $lng = null, $source = null) {

        $source = (null !== $source) ? $source : self::SOURCE_MANUAL;

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
        $this->setName($name);
        $this->setLat($lat);
        $this->setLng($lng);
        $this->setSource($source);
        $this->updateModifiedDate();
    }

    public static function removeAddress(Address $address) {
        foreach ($address->getPois() as $p) {
            /** @var $p POI */
            POI::removePoi($p);
        }
    }

    /**
     * @param POI $poi
     */
    public function assignPoi(POI $poi) {
        $poi->setAddress($this);
        $this->pois->add($poi);
    }

    /**
     * @param POI $poi
     */
    public function removePoi(POI $poi) {
        $this->pois->removeElement($poi);
    }

    /**
     * Returns constructed string from address fields
     * @return mixed|string
     */
    public function toString() {
        $addressName = (null !== $this->getName()) ? $this->getName() : $this->constructAlternativeName();
        $poiSuffix = '';
        //if there are pois, we take the concatinated poi name
        foreach ($this->pois as $key => $poi) {
            if ($key > 0) {
                $poiSuffix . ' ';
            }
            $poiSuffix .= $poi->getName();
        }
        return ('' !== $poiSuffix) ? $addressName . ' (' . $poiSuffix . ')' : $addressName;
    }

    /**
     * @return string
     */
    protected function constructAlternativeName() {
        return $this->getStreet() . ', ' . $this->getPostalCode() . ' ' . $this->getCity() . ', ' . $this->getCountry();
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
     * Set Latitude from float - use GeometryService to set integer<->float precision
     * @param float $lat
     */
    public function setLat($lat) {
        $this->lat = GeometryService::serialize($lat);
    }

    /**
     * Returns Latitude as float - use GeometryService to set integer<->float precision
     * @return float
     */
    public function getLat() {
        return GeometryService::deserialize($this->lat);
    }

    /**
     * Set Longitude from float - use GeometryService to set integer<->float precision
     * @param float $lng
     */
    public function setLng($lng) {
        $this->lng = GeometryService::serialize($lng);
    }

    /**
     * Returns Longitude as float - use GeometryService to set integer<->float precision
     * @return float
     */
    public function getLng() {
        return GeometryService::deserialize($this->lng);
    }

    /**
     * Set the nearest waypoint Latitude from float - use GeometryService to set integer<->float precision
     * @param mixed $nearestLat
     */
    public function setNearestLat($nearestLat) {
        $this->nearestLat = GeometryService::serialize($nearestLat);
    }

    /**
     * Returns the nearest waypoint Latitude as float - use GeometryService to set integer<->float precision
     * @return mixed
     */
    public function getNearestLat() {
        return GeometryService::deserialize($this->nearestLat);
    }

    /**
     * Set the nearest waypoint Longitude from float - use GeometryService to set integer<->float precision
     * @param mixed $nearestLng
     */
    public function setNearestLng($nearestLng) {
        $this->nearestLng = GeometryService::serialize($nearestLng);
    }

    /**
     * Returns the nearest waypoint Latitude as float - use GeometryService to set integer<->float precision
     * @return mixed
     */
    public function getNearestLng() {
        return GeometryService::deserialize($this->nearestLng);
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

    /**
     * @param mixed $source
     */
    public function setSource($source) {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param mixed $house
     */
    public function setBuilding($house) {
        $this->building = $house;
    }

    /**
     * @return mixed
     */
    public function getBuilding() {
        return $this->building;
    }

    /**
     * @return bool
     */
    public function gotCoordinates() {
        return (!empty($this->getLat()) && !empty($this->getLng()));
    }

    /**
     * @return bool
     */
    public function gotNearestCoordinates() {
        return (!empty($this->getNearestLat()) && !empty($this->getNearestLng()));
    }

    /**
     * gets a fast CRC32 Hash from lat and lng, to put address in a hashmap
     * @return string
     */
    public function getHashFromBigIntCoordinates() {
        return hash('md2', $this->lat + $this->lng);
    }
}
