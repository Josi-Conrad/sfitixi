<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 12:40
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class MenuSelectionItemTile extends AbstractTile{

    protected $menuSelectionId;
    protected $displayName;
    protected $isActive;

    public function __construct($menuSelectionId, $displayName, $isActive=false) {
        $this->menuSelectionId = $menuSelectionId;
        $this->displayName = $displayName;
        $this->isActive = $isActive;
    }

    public function getViewIdentifiers(){
        return array('menuitem');
    }

    public function getViewParameters()
    {
        return array('menuSelectionId'=>$this->menuSelectionId, 'displayName'=>$this->displayName,
            'isActive'=>$this->isActive);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menuselectionitem.html.twig';
    }

    public function getName()
    {
        return 'menuselectionitem';
    }
}