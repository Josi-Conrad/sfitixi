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
     * @ORM\ManyToOne(targetEntity="PostalCode")
     * @ORM\JoinColumn(name="postal_code_id", referencedColumnName="id")
     */
    protected $postalCode;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     */
    protected $country;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $street;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10)
     */
    protected $lat;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10)
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
     * @param POI $poi
     */
    public function assignPoi(POI $poi) {
        $this->pois->add($poi);
    }



}
