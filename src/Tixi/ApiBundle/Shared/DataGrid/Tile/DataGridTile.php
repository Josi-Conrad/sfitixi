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

class DataGridTile extends AbstractTile{

    protected $dataGridOutputState;
    protected $gridConfJS;
    protected $gridId;

    public function __construct(DataGridOutputState $dataGridOutputState, $gridConfJS, $gridId) {
        $this->dataGridOutputState = $dataGridOutputState;
        $this->gridConfJS = $gridConfJS;
        $this->gridId = $gridId;
    }

    public function getViewParameters()
    {
        return array('gridId'=>$this->gridId, 'gridState'=>$this->dataGridOutputState, 'gridConfJS'=>$this->gridConfJS);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagrid.html.twig';
    }

    public function getName()
    {
        return 'datagrid';
    }
}