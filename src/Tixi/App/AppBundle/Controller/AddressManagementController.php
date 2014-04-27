<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.04.14
 * Time: 10:53
 */

namespace Tixi\App\AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tixi\App\AppBundle\Address\AddressManagementImpl;

/**
 * Class AddressManagementController
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class AddressManagementController extends Controller{

    /**
     * @Route("/address",name="tixiapp_service_address")
     * @Method({"GET"})
     */
    public function getAddressSuggestionsAction(Request $request) {
        /** @var AddressManagementImpl $addressManager */
        $addressManager = $this->get('tixi_app.addressmanagement');

        $searchStr = $request->get('searchstr');
        $addresses = $addressManager->getAddressSuggestionsByString($searchStr);

        return new Response('ok');
    }

} 