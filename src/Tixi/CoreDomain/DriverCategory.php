<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\DriverCategory
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\DriverCategoryRepositoryDoctrine")
 * @ORM\Table(name="driver_category")
 */
class DriverCategory extends CommonBaseEntity {
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
     * @param $name
     * @return DriverCategory
     */
    protected function __construct($name) {
        parent::__construct();
        $this->setName($name);
    }

    /**
     * @param $name
     * @return DriverCategory
     */
    public static function registerDriverCategory($name) {
        return new DriverCategory($name);
    }

    /**
     * @param $name
     * @return DriverCategory
     */
    public function updateDriverCategoryData($name = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
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
