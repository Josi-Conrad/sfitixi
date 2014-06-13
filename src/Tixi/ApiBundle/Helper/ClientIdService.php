<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 11:58
 */

namespace Tixi\ApiBundle\Helper;


use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Sets ClientID and region search in Google API for a client.
 * Class ClientIdService
 * @package Tixi\ApiBundle\Helper
 */
class ClientIdService extends ContainerAware{

    const ZUGID = 'tixi_zug';
    const DEFAULTID = self::ZUGID;

    protected $mappingArray = array(
        'TIXIZUG' => self::ZUGID
    );

    /**
     * @return null|string
     */
    public function getClientId() {
        $clientId = $this->container->getParameter('tixi_parameter_client');
        $approvedId = null;
        try {
            $approvedId = $this->mappingArray[$clientId];
        }catch (\Exception $e) {
            $approvedId = self::DEFAULTID;
        }
        return $approvedId;
    }

} 