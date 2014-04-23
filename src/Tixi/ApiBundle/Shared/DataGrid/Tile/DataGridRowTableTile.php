<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 21:28
 */

namespace Tixi\ApiBundle\Shared\DataGrid\Tile;


use Tixi\ApiBundle\Shared\DataGrid\DataGridOutputState;
use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class DataGridRowTableTile
 * @package Tixi\ApiBundle\Shared\DataGrid\Tile
 */
class DataGridRowTableTile extends AbstractTile{

    protected $dataGridOutputState;

    /**
     * @param DataGridOutputState $dataGridOutputState
     */
    public function __construct(DataGridOutputState $dataGridOutputState) {
        $this->dataGridOutputState = $dataGridOutputState;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('gridState'=>$this->dataGridOutputState);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridrowstable.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'datagridrowstable';
    }
}