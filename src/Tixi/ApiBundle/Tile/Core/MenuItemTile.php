<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:09
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class MenuItemTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class MenuItemTile extends AbstractTile{

    protected $menuId;
    protected $targetPath;
    protected $displayName;
    protected $isActive;

    /**
     * @param $menuId
     * @param $targetPath
     * @param $displayName
     * @param bool $isActive
     */
    public function __construct($menuId, $targetPath, $displayName, $isActive=false) {
        $this->menuId = $menuId;
        $this->targetPath = $targetPath;
        $this->displayName = $displayName;
        $this->isActive = $isActive;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('menuId'=>$this->menuId, 'targetPath'=>$this->targetPath, 'displayName'=>$this->displayName,
            'isActive'=>$this->isActive);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menuitem.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'menuitem';
    }
}