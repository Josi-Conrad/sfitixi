<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 00:59
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class BackButtonTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $displayText;
    protected $type;

    public function __construct($displayText, $type=null) {
        $this->displayText = $displayText;
        $this->type = (null !== $type) ? $type : self::$defaultType;
    }

    public function getViewParameters() {
        return array('displayText'=>$this->displayText, 'type'=>$this->type);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:backbutton.html.twig';
    }

    public function getName()
    {
        return 'backbutton';
    }
}