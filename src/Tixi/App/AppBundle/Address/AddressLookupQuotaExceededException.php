<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 22:38
 */

namespace Tixi\App\AppBundle\Address;


class AddressLookupQuotaExceededException extends \Exception{

    protected $errorMessage = "Lookup quota for this service reached";

    public function __construct() {
        parent::__construct($this->errorMessage);
    }
} 