<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 21.03.14
 * Time: 13:12
 */

namespace Tixi\App\Address;

use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;

/**
 * Interface AddressManagement
 * @package Tixi\App
 */
interface AddressManagement {

    /**
     * This function supports fuzzy search by fulltext search engine on DB or lookup services like google.
     * Returns Suggestions in an array of AddressHandleDTOs.
     *
     * @param $addressString
     * @return AddressHandleDTO[]
     */
    public function getAddressSuggestionsByString($addressString);

    /**
     * Get array of AddressHandleDTOs with size of one containing the users home address associated with the given
     * user id
     *
     * @param $passengerId
     * @return mixed
     */
    public function getAddressHandleByPassengerId($passengerId);

    /**
     * Will query  AddressString on a lookup service like google and takes first best suggestion given.
     * Addresstring should be valid for exact queries. Returns Suggestion as an AddressHandleDTO
     *
     * @param $addressString
     * @return AddressHandleDTO
     */
    public function getAddressInformationByString($addressString);

    /**
     * Handles a new Address object if register new one or get an existing one
     *
     * @param AddressHandleDTO $addressHandleDTO
     * @return mixed
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO);

}