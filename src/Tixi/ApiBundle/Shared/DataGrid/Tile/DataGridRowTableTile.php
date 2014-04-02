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

class DataGridRowTableTile extends AbstractTile{

    protected $dataGridOutputState;

    public function __construct(DataGridOutputState $dataGridOutputState) {
        $this->dataGridOutputState = $dataGridOutputState;
    }

    public function getViewParameters()
    {
        return array('gridState'=>$this->dataGridOutputState);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridrowstable.html.twig';
    }

    public function getName()
    {
        return 'datagridrowstable';
    }
}