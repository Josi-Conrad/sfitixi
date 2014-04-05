<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */
namespace Tixi\ApiBundle\Controller;


use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HomeController
 * @package Tixi\ApiBundle\Controller
 * @Route("/home")
 * @Breadcrumb("home.panel.name", route="tixiapi_home")
 */
class HomeController extends Controller {
    /**
     * @Route("",name="tixiapi_home")
     * @Method({"GET","POST"})
     */
    public function getHomeAction(Request $request) {
        $template = 'TixiApiBundle:Home:get.html.twig';
        return $this->render($template);
    }
}