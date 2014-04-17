<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 15:30
 */

namespace Tixi\CoreDomain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Tixi\CoreDomain\Shared\CommonBaseEntity;
use Tixi\CoreDomain\Shared\Entity;

/**
 * Tixi\CoreDomain\POIKeyword
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\POIKeywordRepositoryDoctrine")
 * @ORM\Table(name="poi_keyword")
 */
class POIKeyword extends CommonBaseEntity implements Entity{
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
     * @ORM\ManyToMany(targetEntity="POI", mappedBy="keywords")
     */
    protected $pois;

    /**
     *
     */
    protected  function __construct() {
        $this->pois = new ArrayCollection();
        parent::__construct();
    }

    /**
     * @param $name
     * @return POIKeyword
     */
    public static function registerPOIKeyword($name){
        $poiKeyword =  new POIKeyword();
        $poiKeyword->setName($name);
        return $poiKeyword;
    }

    public function updateData($name=null) {
        if(null !== $name) {
            $this->name = $name;
        }
    }

    public function assignPOI($poi) {
        $this->pois->add($poi);
    }

    public function unsignPOI($poi) {
        $this->pois->removeElement($poi);
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
     * @param mixed $pois
     */
    public function setPois($pois) {
        $this->pois = $pois;
    }

    /**
     * @return mixed
     */
    public function getPois() {
        return $this->pois;
    }
}
