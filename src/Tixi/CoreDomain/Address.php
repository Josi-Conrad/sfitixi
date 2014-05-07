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
 * Tixi\CoreDomain\Address
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine")
 * @ORM\Table(name="address")
 */
class Address extends CommonBaseEntity{

    const SOURCE_MANUAL = 'manual';
    const SOURCE_GOOGLE = 'google';

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
     * @ORM\Column(type="string", length=30)
     */
    protected $source;
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lat;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lng;


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
                                           $name = null, $lat = null, $lng = null, $source=self::SOURCE_MANUAL) {
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
                                           $name = null, $lat = null, $lng = null, $source=self::SOURCE_MANUAL) {

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
        return (null !== $this->getName()) ? $this->getName() : $this->constructAlternativeName();
    }

    /**
     * @return string
     */
    protected function constructAlternativeName() {
        return $this->getStreet().', '.$this->getPostalCode().' '.$this->getCity().', '.$this->getCountry();
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
     * Set Latitude as integer - use GeometryService to set integer<->float precision
     * @param integer $lat
     */
    public function setLat($lat) {
        $this->lat = $lat;
    }

    /**
     * Returns Latitude as integer - use GeometryService to set integer<->float precision
     * @return integer
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * Set Longitude as integer - use GeometryService to set integer<->float precision
     * @param integer $lng
     */
    public function setLng($lng) {
        $this->lng = $lng;
    }

    /**
     * Return Longitude as integer - use GeometryService to set integer<->float precision
     * @return integer
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
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }


}
