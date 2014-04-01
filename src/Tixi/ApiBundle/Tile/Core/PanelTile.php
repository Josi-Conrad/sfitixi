<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 23:52
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class PanelTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $headerDisplayText;
    protected $type;

    public function __construct($headerDisplayText, $type=null) {
        $this->type = (null !== $type) ? $type : self::$defaultType;
        $this->headerDisplayText = $headerDisplayText;
    }

    public function getViewParameters()
    {
        return array('headerDisplayText'=>$this->headerDisplayText, 'type'=>$this->type);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:panel.html.twig';
    }

    public function getName()
    {
        return 'panel';
    }
}