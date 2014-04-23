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
 * Tixi\CoreDomain\Handicap
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine")
 * @ORM\Table(name="handicap")
 */
class Handicap extends CommonBaseEntity{
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

    protected function __construct() {
        parent::__construct();
    }

    /**
     * @param $name
     * @return Handicap
     */
    public static function registerHandicap($name){
        $handicap = new Handicap();
        $handicap->setName($name);
        return $handicap;
    }

    /**
     * @param null $name
     */
    public function updateData($name=null) {
        if(null !== $name) {
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
