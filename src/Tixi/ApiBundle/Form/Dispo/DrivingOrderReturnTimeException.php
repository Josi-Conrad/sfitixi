<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 12.06.14
 * Time: 16:26
 */

namespace Tixi\ApiBundle\Form\Dispo;


class DrivingOrderReturnTimeException extends \Exception{
    /**
     * @var string
     */
    protected $errorMessage = "No pickup time was provided for return order";

    public function __construct() {
        parent::__construct($this->errorMessage);
    }
} 