<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:53
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Time;

/**
 * Tixi\CoreDomain\Dispo\ShiftType
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\ShiftTypeRepositoryDoctrine")
 * @ORM\Table(name="shift_type")
 */
class ShiftType {
    /**
     * @ORM\Id
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $start;
    /**
     * @ORM\Column(type="datetime")
     */
    protected $end;

    public function __construct() {
        $this->shifts = new ArrayCollection();
    }

    /**
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isResponsibleForTime(\DateTime $dateTime) {
        //TODO:
        return true;
    }

} 