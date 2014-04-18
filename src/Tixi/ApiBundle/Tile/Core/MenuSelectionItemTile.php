<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 12:40
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class MenuSelectionItemTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class MenuSelectionItemTile extends AbstractTile{

    protected $menuSelectionId;
    protected $displayName;
    protected $isActive;

    /**
     * @param $menuSelectionId
     * @param $displayName
     * @param bool $isActive
     */
    public function __construct($menuSelectionId, $displayName, $isActive=false) {
        $this->menuSelectionId = $menuSelectionId;
        $this->displayName = $displayName;
        $this->isActive = $isActive;
    }

    /**
     * @return array
     */
    public function getViewIdentifiers(){
        return array('menuitem');
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('menuSelectionId'=>$this->menuSelectionId, 'displayName'=>$this->displayName,
            'isActive'=>$this->isActive);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menuselectionitem.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'menuselectionitem';
    }
}