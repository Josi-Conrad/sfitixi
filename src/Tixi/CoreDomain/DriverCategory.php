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
 * Tixi\CoreDomain\DriverCategory
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine")
 * @ORM\Table(name="driver_category")
 */
class DriverCategory {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $name;

    /**
     * @param $name
     * @return DriverCategory
     */
    private function __construct($name) {
        $this->setName($name);
    }

    /**
     * @param $name
     * @return DriverCategory
     */
    public static function registerDriverCategory($name){
        return new DriverCategory($name);
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


}
