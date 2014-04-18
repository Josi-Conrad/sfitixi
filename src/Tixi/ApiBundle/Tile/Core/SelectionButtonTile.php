<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 09:40
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class SelectionButtonTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class SelectionButtonTile extends AbstractTile{

    protected $displayName;
    protected $selectionButtonId;

    /**
     * @param $selectionButtonId
     * @param $displayName
     */
    public function __construct($selectionButtonId, $displayName) {
        $this->selectionButtonId = $selectionButtonId;
        $this->displayName = $displayName;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('selectionButtonId'=>$this->selectionButtonId, 'displayName'=>$this->displayName);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:selectionbutton.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'selectionbutton';
    }
}