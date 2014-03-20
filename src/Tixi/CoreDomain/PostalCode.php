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
 * @ORM\Entity
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
    protected $postalCode;

    /**
     * @param $postalCode
     */
    private function __construct($postalCode) {
        $this->postalCode($postalCode);
    }

    /**
     * @param $postalCode
     * @return PostalCode
     */
    public static function registerPostalCode($postalCode) {
        return new PostalCode($postalCode);
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
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getPostalCode() {
        return $this->postalCode;
    }
}
