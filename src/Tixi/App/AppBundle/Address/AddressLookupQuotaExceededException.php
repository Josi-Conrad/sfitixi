<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.04.14
 * Time: 22:38
 */

namespace Tixi\App\AppBundle\Address;

/**
 * Class AddressLookupQuotaExceededException
 * @package Tixi\App\AppBundle\Address
 */
class AddressLookupQuotaExceededException extends \Exception{
    /**
     * @var string
     */
    protected $errorMessage = "Lookup quota for this service reached";

    public function __construct() {
        parent::__construct($this->errorMessage);
    }
} 