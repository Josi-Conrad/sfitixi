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
 * Tixi\CoreDomain\Insurance
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\InsuranceRepositoryDoctrine")
 * @ORM\Table(name="insurance")
 */
class Insurance extends CommonBaseEntity {
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

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @param null $memo
     * @return Insurance
     */
    public static function registerInsurance($name, $memo = null) {
        $insurance = new Insurance();
        $insurance->setName($name);
        $insurance->setMemo($memo);
        return $insurance;
    }

    /**
     * @param null $name
     * @param null $memo
     */
    public function updateData($name = null, $memo = null) {
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
