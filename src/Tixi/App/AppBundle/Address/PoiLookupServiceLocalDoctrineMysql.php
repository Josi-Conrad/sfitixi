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
use Tixi\CoreDomainBundle\Repository\AddressRepositoryDoctrine;

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
        AGAINST ('.$searchString.' IN BOOLEAN MODE)
        LIMIT 0, " . $this->getLookupLimit();

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->container->get('address_repository');
        $addresses = array();
        foreach($results as $result) {
            $address = $addressRepository->find($result->getId());
            $addresses[] = AddressHandleAssembler::toAddressHandleDTO($address, $result->getName());
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