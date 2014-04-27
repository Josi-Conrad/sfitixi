<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 21:31
 */

namespace Tixi\App\AppBundle\Address;


use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Tixi\App\AppBundle\Interfaces\AddressHandleAssembler;

class AddressLookupServiceLocalDoctrineMysql extends AddressLookupService{

    public function hasLookupQuota()
    {
        return false;
    }

    protected function getAddressHandlingDTOs($lookupStr)
    {
        $searchString = $this->constructFulltextSeachString($lookupStr);

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('Tixi\CoreDomain\Address', 'a');

        $sql = "SELECT a.id, a.street, a.postalCode, a.city, a.country, a.lat, a.lng, a.source FROM address a
        WHERE MATCH (name, street, postalCode, city, country, source)
        AGAINST ('.$searchString.' IN BOOLEAN MODE)
        LIMIT 0, " . $this->getLookupLimit();

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        $addresses = array();
        /** @var $result Address */
        foreach ($results as $result) {
            $addresses[] = AddressHandleAssembler::toAddressHandleDTO($result);
        }
        return $addresses;
    }

    protected function constructFulltextSeachString($lookupStr) {
        $words = explode(' ',$lookupStr);
        array_walk($words, function(&$word, $key) {
            $word = '+' . $word . '* ';
        });
        return implode(' ',$words);
    }
}