<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 14:12
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class TextLinkTile extends AbstractTile{

    protected $targetSrc;
    protected $displayName;
    protected $resolvedLater;

    public function __construct($targetSrc, $displayName, $resolvedLater = false, $replaceId=null, $replaceWith=null) {
        $this->displayName = $displayName;
        $this->targetSrc = (null !== $replaceId && null !== $replaceWith) ?
            str_replace($replaceId, $replaceWith, $targetSrc) :
            $targetSrc;
        $this->resolvedLater = $resolvedLater;
    }

    public function getViewParameters() {
        return array('resolvedLater'=>$this->resolvedLater, 'displayName'=>$this->displayName, 'targetSrc'=>$this->targetSrc);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:textlink.html.twig';
    }

    public function getName()
    {
        return 'textlink';
    }
}