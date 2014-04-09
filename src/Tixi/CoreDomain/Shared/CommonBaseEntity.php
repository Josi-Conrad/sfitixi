<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 17:42
 */

namespace Tixi\CoreDomain\Shared;

use Doctrine\ORM\Mapping as ORM;
use Tixi\ApiBundle\Helper\DateTimeService;

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
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $modifiedDateTime;

    protected  function __construct() {
        $this->isDeleted = false;
        $this->creationDateTime = DateTimeService::getUTCNowDateTime();
        $this->modifiedDateTime = DateTimeService::getUTCNowDateTime();
    }

    protected function updateModifiedDate() {
        $this->modifiedDateTime = DateTimeService::getUTCNowDateTime();
    }
    /**
     * @return \DateTime
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }
    /**
     * @return \DateTime
     */
    public function getModifiedDateTime()
    {
        return $this->modifiedDateTime;
    }

    public function deleteLogically() {
        $this->isDeleted = true;
    }

    public function undeleteLogically() {
        $this->isDeleted = false;
    }
}