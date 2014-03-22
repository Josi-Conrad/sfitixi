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
 * Tixi\CoreDomain\City
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\CityRepositoryDoctrine")
 * @ORM\Table(name="city")
 */
class City {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $name;

    /**
     * @param $city
     */
    private function __construct($name) {
        $this->name = $name;
    }

    /**
     * @param $city
     * @return City
     */
    public static function registerCity($name) {
        $city = new City($name);
        return $city;
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
     * @param mixed $city
     */
    public function setName($city) {
        $this->name = $city;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

}
