<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 21:57
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class LinkButtonTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $targetSrc;
    protected $type;
    protected $displayText;

    public function __construct($targetSrc, $displayText, $type=null, $replaceId=null, $replaceWith=null) {
        $this->displayText = $displayText;
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
        return array('displayText'=>$this->displayText, 'tagetSrc'=>$this->targetSrc, 'type'=>$this->type);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:linkbutton.html.twig';
    }

    public function getName()
    {
        return 'linkbutton';
    }
}