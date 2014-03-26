<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class HomeController
 * @package Tixi\ApiBundle\Controller
 * @Breadcrumb("Home", route="tixiapi_home")
 * @Route("/home")
 */
class HomeController extends Controller {
    /**
     * @Route("",name="tixiapi_home")
     * @Method({"GET","POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getHomeAction(Request $request) {
        $template = 'TixiApiBundle:Home:get.html.twig';
        return $this->render($template);
    }
}