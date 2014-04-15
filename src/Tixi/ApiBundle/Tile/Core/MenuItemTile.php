<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:09
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class MenuItemTile extends AbstractTile{

    protected $menuId;
    protected $targetPath;
    protected $displayName;
    protected $isActive;

    public function __construct($menuId, $targetPath, $displayName, $isActive=false) {
        $this->menuId = $menuId;
        $this->targetPath = $targetPath;
        $this->displayName = $displayName;
        $this->isActive = $isActive;
    }

    public function getViewParameters()
    {
        return array('menuId'=>$this->menuId, 'targetPath'=>$this->targetPath, 'displayName'=>$this->displayName,
            'isActive'=>$this->isActive);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menuitem.html.twig';
    }

    public function getName()
    {
        return 'menuitem';
    }
}