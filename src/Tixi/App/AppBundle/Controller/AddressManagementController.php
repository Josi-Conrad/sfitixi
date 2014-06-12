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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tixi\App\Address\AddressManagement;

/**
 * Class AddressManagementController
 * @package Tixi\App\AppBundle\Controller
 * @Route("/service")
 */
class AddressManagementController extends Controller {
    /**
     * lookahead for address string, example:
     * /address?requeststate=search_state&searchstr=laupenring+2
     * @Route("/address",name="tixiapp_service_address")
     * @Method({"GET"})
     */
    public function getAddressSuggestionsAction(Request $request) {
        /** @var AddressManagement $addressManager */
        $addressManager = $this->get('tixi_app.addressmanagement');

        $requestState = $request->get('requeststate');
        $response = new JsonResponse();
        $addresses = null;
        if ($requestState === 'search_state') {
            $searchStr = $request->get('searchstr');
            $addresses = $addressManager->getAddressSuggestionsByString($searchStr);
        } elseif ($requestState === 'user_state') {
            $passengerId = $request->get('passengerid');
            $addresses = $addressManager->getAddressHandleByPassengerId($passengerId);
        } else {
            //
        }
        $response->setData(array('models' => $addresses));
        return $response;
    }

} 