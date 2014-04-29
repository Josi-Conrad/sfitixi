<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 11:58
 */

namespace Tixi\ApiBundle\Helper;


use Symfony\Component\DependencyInjection\ContainerAware;

class ClientIdService extends ContainerAware{

    const ZUGID = 'tixi_zug';
    const DEFAULTID = self::ZUGID;

    protected $mappingArray = array(
        'TIXIZUG' => self::ZUGID
    );

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