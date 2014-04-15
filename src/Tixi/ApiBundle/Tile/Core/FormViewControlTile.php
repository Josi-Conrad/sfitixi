<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:17
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class FormViewControlTile extends AbstractTile{

    public function __construct($editPath) {
        $this->add(new LinkButtonTile('edit', $editPath, 'Editieren', LinkButtonTile::$primaryType));
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formcontrol.html.twig';
    }

    public function getName()
    {
        return 'formviewcontrol';
    }
}