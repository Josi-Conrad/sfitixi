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
 * Tixi\CoreDomain\PersonCategory
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\PersonCategoryRepositoryDoctrine")
 * @ORM\Table(name="person_category")
 */
class PersonCategory extends CommonBaseEntity {
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $memo;

    /**
     * @return PersonCategory
     */
    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param null $memo
     * @return PersonCategory
     */
    public static function registerPersonCategory($name, $memo = null) {
        $personCategory = new PersonCategory();
        $personCategory->setName($name);
        $personCategory->setMemo($memo);
        return $personCategory;
    }

    /**
     * @param $name
     * @param null $memo
     * @return PersonCategory
     */
    public function updatePersonCategoryData($name = null, $memo = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        $this->setMemo($memo);
        $this->updateModifiedDate();
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

    /**
     * @param mixed $memo
     */
    public function setMemo($memo) {
        $this->memo = $memo;
    }

    /**
     * @return mixed
     */
    public function getMemo() {
        return $this->memo;
    }

}
