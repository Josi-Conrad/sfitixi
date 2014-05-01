<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 01.05.14
 * Time: 10:38
 */

namespace Tixi\App\AppBundle\Routing;

/**
 * Class RoutingMachineException
 * @package Tixi\App\AppBundle\Routing
 */
class RoutingMachineException extends \Exception {
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
} 