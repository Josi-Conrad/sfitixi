<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:40
 */

namespace Tixi\ApiBundle\Menu;



class MenuTwigExtension extends \Twig_Extension {

    /**
     * @var MenuService $menuService
     */
    protected $menuService;

    public function getFunctions() {
        return array(
           new \Twig_SimpleFunction('renderMenu',function($activeMenuItem=null) {
               return $this->menuService->createMenu($activeMenuItem);
           }, array('is_safe' => array('html')))
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        'menuservice';
    }

    public function setMenuService(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
}