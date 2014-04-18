<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 22:26
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class RootPanel
 * @package Tixi\ApiBundle\Tile\Core
 */
class RootPanel extends AbstractTile{

    protected $menuId;
    protected $headerDisplayText;
    protected $headerDisplaySubtitleText;

    /**
     * @param $menuId
     * @param $headerDisplayText
     * @param string $headerDisplaySubtitleText
     */
    public function __construct($menuId, $headerDisplayText, $headerDisplaySubtitleText='') {
        $this->menuId = $menuId;
        $this->headerDisplayText = $headerDisplayText;
        $this->headerDisplaySubtitleText = $headerDisplaySubtitleText;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('menuId'=>$this->menuId, 'headerDisplayText'=>$this->headerDisplayText, 'headerDisplaySubtitleText'=>$this->headerDisplaySubtitleText);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:rootpanel.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'rootpanel';
    }
}