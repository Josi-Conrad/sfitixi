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
    protected $streetName;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $streetNr;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $postCode;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $geocode;

    private function __construct() {
        $this->pois = new ArrayCollection();
    }

    /**
     * @param $streetName
     * @param $streetNr
     * @param $city
     * @param $postCode
     * @param $country
     * @param null $geocode
     * @return Address
     */
    public static function registerAddress($streetName, $streetNr, $city, $postCode, $country, $geocode = null) {
        $address = new Address();

        $address->setStreetName($streetName);
        $address->setStreetNr($streetNr);
        $address->setCity($city);
        $address->setPostCode($postCode);
        $address->setCountry($country);
        if(!is_null($geocode)){$address->setGeocode($geocode);}

        return $address;
    }

    /**
     * @param null $streetName
     * @param null $streetNr
     * @param null $city
     * @param null $postCode
     * @param null $country
     * @param null $geocode
     * @param null $geocode
     */
    public function updateBasicData($streetName=null, $streetNr=null, $city=null, $postCode=null, $country=null,
                                    $geocode =null) {
        if(!is_null($streetName)) {$this->streetName=$streetName;}
        if(!is_null($streetNr)) {$this->streetNr=$streetNr;}
        if(!is_null($city)) {$this->city=$city;}
        if(!is_null($postCode)) {$this->postCode=$postCode;}
        if(!is_null($country)) {$this->country=$country;}
        if(!is_null($geocode)) {$this->geocode=$geocode;}
    }

    /**
     * @param POI $poi
     */
    public function assignPoi(POI $poi) {
        $this->pois->add($poi);
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
     * @param mixed $geocode
     */
    public function setGeocode($geocode) {
        $this->geocode = $geocode;
    }

    /**
     * @return mixed
     */
    public function getGeocode() {
        return $this->geocode;
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
     * @param mixed $postCode
     */
    public function setPostCode($postCode) {
        $this->postCode = $postCode;
    }

    /**
     * @return mixed
     */
    public function getPostCode() {
        return $this->postCode;
    }

    /**
     * @param mixed $streetName
     */
    public function setStreetName($streetName) {
        $this->streetName = $streetName;
    }

    /**
     * @return mixed
     */
    public function getStreetName() {
        return $this->streetName;
    }

    /**
     * @param mixed $streetNr
     */
    public function setStreetNr($streetNr) {
        $this->streetNr = $streetNr;
    }

    /**
     * @return mixed
     */
    public function getStreetNr() {
        return $this->streetNr;
    }


}
