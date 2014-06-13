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
use Tixi\CoreDomain\Address;

/**
 * Class AddressLookupServiceLocalDoctrineMysql
 * @package Tixi\App\AppBundle\Address
 */
class AddressLookupServiceLocalDoctrineMysql extends AddressLookupService {
    /**
     * This class makes use of mysql fulltext index.
     * Please make sure that the following defaults are set in the mysql config file:
     * ft_min_word_len=1
     * innodb_ft_min_token_size=1
     */

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
        $rsm->addRootEntityFromClassMetadata('Tixi\CoreDomain\Address', 'a');

        $sql = "SELECT a.id, a.street, a.postalCode, a.city, a.country, a.lat, a.lng, a.source FROM address a
        WHERE MATCH (name, street, postalCode, city, country, source)
        AGAINST ('.$searchString.' IN BOOLEAN MODE) AND a.isDeleted = 0
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