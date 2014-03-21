<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 21.03.14
 * Time: 13:12
 */

namespace Tixi\App;

use Tixi\App\AppBundle\Interfaces\AddressHandleDTO;

interface AddressManagement {

    /**
     * @param $addressString
     * @return AddressHandleDTO
     */
    public function getAddressSuggestionsByString($addressString);

    /**
     * @param AddressHandleDTO $addressHandleDTO
     * @return mixed
     */
    public function handleAddress(AddressHandleDTO $addressHandleDTO);

}