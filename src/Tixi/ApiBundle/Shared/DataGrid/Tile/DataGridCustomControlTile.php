<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 22:04
 */

namespace Tixi\ApiBundle\Shared\DataGrid\Tile;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class DataGridCustomControlTile
 * @package Tixi\ApiBundle\Shared\DataGrid\Tile
 */
class DataGridCustomControlTile extends AbstractTile{
    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridcustomcontrol.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'datagridcustomcontrol';
    }
}