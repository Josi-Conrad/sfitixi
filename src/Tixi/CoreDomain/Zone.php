<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 28.03.14
 * Time: 13:54
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Driver;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * Tixi\CoreDomain\Zone
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\ZoneRepositoryDoctrine")
 * @ORM\Table(name="zone")
 */
class Zone extends CommonBaseEntity {

    const MAXZONEPRIORITY = 1000;
    const UNCLASSIFIEDPRIORITY = 1001;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;
    /**
     * @ORM\Column(type="smallint")
    */
    protected $priority;
    /**
     * @ORM\OneToMany(targetEntity="ZonePlan", mappedBy="zone")
     */
    protected $zonePlans;



    protected function __construct() {
        $this->zonePlans = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $name
     * @param $priority
     * @throws \InvalidArgumentException
     * @return Zone
     */
    public static function registerZone($name, $priority) {
        $zone = new Zone();
        if($priority>self::MAXZONEPRIORITY) {
            throw new \InvalidArgumentException('priority must be smaller than '.self::MAXZONEPRIORITY);
        }
        $zone->setName($name);
        $zone->setPriority($priority);
        return $zone;
    }

    public static function createUnclassifiedZone() {
        $zone = new Zone();
        $zone->setName('zone.unclassified');
        $zone->setPriority(self::UNCLASSIFIEDPRIORITY);
        return $zone;
    }

    /**
     * @param null $name
     * @param null $priority
     * @throws \InvalidArgumentException
     */
    public function updateZone($name = null, $priority = null) {
        if (!empty($name)) {
            $this->setName($name);
        }
        if(!empty($priority)) {
            if($priority>self::MAXZONEPRIORITY) {
                throw new \InvalidArgumentException('priority must be smaller than '.self::MAXZONEPRIORITY);
            }
            $this->setPriority($priority);
        }
        $this->updateModifiedDate();
    }

    public function isUnclassified() {
        return $this->priority === self::UNCLASSIFIEDPRIORITY;
    }

    /**
     * @param ZonePlan $zonePlan
     */
    public function assignZonePlan(ZonePlan $zonePlan) {
        $this->zonePlans->add($zonePlan);
    }

    /**
     * @param ZonePlan $zonePlan
     */
    public function removeZonePlan(ZonePlan $zonePlan) {
        $this->zonePlans->remove($zonePlan);
    }

    /**
     * @return mixed
     */
    public function getZonePlans() {
        return $this->zonePlans;
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
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }


}