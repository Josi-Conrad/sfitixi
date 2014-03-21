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
 * Tixi\CoreDomain\PostalCode
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\PostalCodeRepositoryDoctrine")
 * @ORM\Table(name="postal_code")
 */
class PostalCode {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    protected $code;

    /**
     * @param $postalCode
     */
    private function __construct($code) {
        $this->code = $code;
    }

    /**
     * @param $postalCode
     * @return PostalCode
     */
    public static function registerPostalCode($code) {
        return new PostalCode($code);
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
     * @param mixed $postalCode
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode() {
        return $this->code;
    }
}
