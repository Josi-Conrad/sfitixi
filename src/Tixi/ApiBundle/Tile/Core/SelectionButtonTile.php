<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 09:40
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class SelectionButtonTile extends AbstractTile{

    protected $displayName;

    public function __construct($displayName) {
        $this->displayName = $displayName;
    }

    public function getViewParameters()
    {
        return array('displayName'=>$this->displayName);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:selectionbutton.html.twig';
    }

    public function getName()
    {
        return 'selectionbutton';
    }
}