<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 12.06.14
 * Time: 16:23
 */

namespace Tixi\ApiBundle\Form\Dispo;


class DrivingOrderOutwardTimeException extends \Exception{
    /**
     * @var string
     */
    protected $errorMessage = "No pickup time was provided for outward order";

    public function __construct() {
        parent::__construct($this->errorMessage);
    }
} 