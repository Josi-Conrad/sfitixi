<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:17
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class FormViewControlTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class FormViewControlTile extends AbstractTile{
    /**
     * @param $editPath
     */
    public function __construct($editPath) {
        $this->add(new LinkButtonTile('edit', $editPath, 'Editieren', LinkButtonTile::$primaryType));
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formcontrol.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'formviewcontrol';
    }
}