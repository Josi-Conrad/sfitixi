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
 * Tixi\CoreDomain\Country
 *
 * @ORM\Entity
 * @ORM\Table(name="country")
 */
class Country {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $country;

    /**
     * @param $country
     */
    private function __construct($country) {
        $this->country($country);
    }

    /**
     * @param $country
     * @return Country
     */
    public static function registerCity($country) {
        return new Country($country);
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

}
