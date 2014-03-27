<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.03.14
 * Time: 00:00
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class CustomTile extends AbstractTile{

    protected $templateName;

    public function __construct($templateName) {
        $this->templateName = $templateName;
    }

    public function getViewParameters()
    {
        return array();
    }

    public function getTemplateName()
    {
        return $this->templateName;
    }

    public function getName()
    {
        return 'customtile';
    }
}