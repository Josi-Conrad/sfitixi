<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 21:57
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class ActionButtonTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $targetSrc;
    protected $type;
    protected $displayName;

    public function __construct($targetSrc, $displayName, $type=null, $replaceId=null, $replaceWith=null) {
        $this->displayName = $displayName;
        $this->type = (null !== $type) ? $type : self::$defaultType;
        $this->targetSrc = (null !== $replaceId && null !== $replaceWith) ?
            str_replace($replaceId, $replaceWith, $targetSrc) :
            $targetSrc;
    }

    public function getViewIdentifiers() {
        return array('panelcontent');
    }

    public function getViewParameters()
    {
        return array('displayName'=>$this->displayName, 'tagetSrc'=>$this->targetSrc, 'type'=>$this->type);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:actionbutton.html.twig';
    }

    public function getName()
    {
        return 'actionbutton';
    }
}