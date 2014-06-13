<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 18.05.14
 * Time: 11:21
 */

namespace Tixi\App\AppBundle\Address;


use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Tixi\App\AppBundle\Interfaces\AddressHandleAssembler;
use Tixi\CoreDomain\AddressRepository;
use Tixi\CoreDomain\POI;
use Tixi\CoreDomain\POIRepository;
use Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine;

/**
 * Class PoiLookupServiceLocalDoctrineMysql
 * @package Tixi\App\AppBundle\Address
 */
class PoiLookupServiceLocalDoctrineMysql extends AddressLookupService{
    /**
     * @return bool|mixed
     */
    public function hasLookupQuota() {
        return false;
    }

    /**
     * @param $lookupStr
     * @return array|mixed
     */
    protected function getAddressHandlingDTOs($lookupStr) {
        $searchString = $this->constructFulltextSearchString($lookupStr);

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('Tixi\CoreDomain\POI', 'p');

        $sql = "SELECT p.id, p.name FROM poi p
        WHERE MATCH (p.name)
        AGAINST ('.$searchString.' IN BOOLEAN MODE) AND p.isDeleted = 0
        LIMIT 0, " . $this->getLookupLimit();

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();
        $addresses = array();
        /** @var POIRepository $poiRepository */
        $poiRepository = $this->container->get('poi_repository');
        foreach($results as $result) {
            /** @var POI $poi */
            $id = $result->getId();
            $this->getEntityManager()->close();
            $poi = $poiRepository->find($id);
            $address = $poi->getAddress();
            $addresses[] = AddressHandleAssembler::toAddressHandleDTO($address);
        }

        return $addresses;
    }

    /**
     * @param $lookupStr
     * @return string
     */
    protected function constructFulltextSearchString($lookupStr) {
        $words = explode(' ', $lookupStr);
        array_walk($words, function (&$word, $key) {
            $word = '+' . $word . '* ';
        });
        return implode(' ', $words);
    }

    /**
     * not implemented on local lookup service
     * @param $lookupStr
     * @return mixed
     */
    protected function getSingleAddressHandleDTO($lookupStr) {
        return null;
    }
} 