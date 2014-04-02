<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 22:04
 */

namespace Tixi\ApiBundle\Shared\DataGrid\Tile;


use Tixi\ApiBundle\Tile\AbstractTile;

class DataGridCustomControlTile extends AbstractTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridcustomcontrol.html.twig';
    }

    public function getName()
    {
        return 'datagridcustomcontrol';
    }
}