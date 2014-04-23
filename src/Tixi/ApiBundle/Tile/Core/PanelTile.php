<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 23:52
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class PanelTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class PanelTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $headerDisplayText;
    protected $type;

    /**
     * @param $headerDisplayText
     * @param null $type
     */
    public function __construct($headerDisplayText, $type=null) {
        $this->type = (null !== $type) ? $type : self::$defaultType;
        $this->headerDisplayText = $headerDisplayText;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('headerDisplayText'=>$this->headerDisplayText, 'type'=>$this->type);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:panel.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'panel';
    }
}