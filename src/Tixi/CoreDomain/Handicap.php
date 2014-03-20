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
 * Tixi\CoreDomain\Handicap
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\HandicapRepositoryDoctrine")
 * @ORM\Table(name="handicap")
 */
class Handicap {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50, unique=true)
     */
    protected $name;

    /**
     * @param $name
     * @return Handicap
     */
    private function __construct($name) {
        $this->setName($name);
    }

    /**
     * @param $name
     * @return Handicap
     */
    public static function registerHandicap($name){
        return new Handicap($name);
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
