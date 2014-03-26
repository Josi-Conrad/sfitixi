<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 22:26
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class RootPanel extends AbstractTile{

    protected $headerDisplayText;

    public function __construct($headerDisplayText) {
        $this->headerDisplayText = $headerDisplayText;
    }

    public function getViewParameters()
    {
        return array('headerDisplayText'=>$this->headerDisplayText);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:rootpanel.html.twig';
    }

    public function getName()
    {
        return 'rootpanel';
    }
}