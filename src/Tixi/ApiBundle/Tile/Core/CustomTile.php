<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.03.14
 * Time: 00:00
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class CustomTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class CustomTile extends AbstractTile{

    protected $templateName;

    /**
     * @param $templateName
     */
    public function __construct($templateName) {
        $this->templateName = $templateName;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array();
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'customtile';
    }
}