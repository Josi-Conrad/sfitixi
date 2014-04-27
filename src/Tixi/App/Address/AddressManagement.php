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
     * Returns Address Object Suggestions from a string input (like google search)
     *
     * @param $addressString
     * @return AddressHandleDTO
     */
    public function getAddressSuggestionsByString($addressString);

    /**
     * Handles a new Address object if register new one or get an existing one
     *
     * @param AddressHandleDTO $addressHandleDTO
     * @return mixed
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO);

}