<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 17:42
 */

namespace Tixi\CoreDomain\Shared;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommonBaseEntity
 * @package Tixi\CoreDomain\Shared
 * @ORM\MappedSuperclass()
 */
class CommonBaseEntity {
    /**
     * @ORM\Column(type="boolean")
     */
    protected $isDeleted;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $creationDateTime;
    /**
     * @ORM\Column(type="utcdatetime")
     */
    protected $modifiedDateTime;

    /**
     * Contructor of BaseEntity with creationTime and modifiedTime
     */
    protected function __construct() {
        $this->isDeleted = false;
        $this->creationDateTime = new \DateTime();
        $this->modifiedDateTime = new \DateTime();
    }

    /**
     * Update of BaseEntity with modifiedTime
     */
    protected function updateModifiedDate() {
        $this->modifiedDateTime = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreationDateTime() {
        return $this->creationDateTime;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedDateTime() {
        return $this->modifiedDateTime;
    }

    /**
     * for common base entities, we delete them only logical
     */
    public function deleteLogically() {
        $this->isDeleted = true;
    }

    /**
     * for common base entities, we delete them only logical -> undelete
     */
    public function undeleteLogically() {
        $this->isDeleted = false;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return !$this->isDeleted;
    }
}