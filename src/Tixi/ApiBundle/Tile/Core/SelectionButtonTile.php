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
    protected $selectionButtonId;

    public function __construct($selectionButtonId, $displayName) {
        $this->selectionButtonId = $selectionButtonId;
        $this->displayName = $displayName;
    }

    public function getViewParameters()
    {
        return array('selectionButtonId'=>$this->selectionButtonId, 'displayName'=>$this->displayName);
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