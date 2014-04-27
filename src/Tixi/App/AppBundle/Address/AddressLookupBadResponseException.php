<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 15:02
 */

namespace Tixi\App\AppBundle\Address;


class AddressLookupBadResponseException extends \Exception{

    protected $errorMessage = "A bad response has been received from service.";

    public function __construct() {
        parent::__construct($this->errorMessage);
    }
} 