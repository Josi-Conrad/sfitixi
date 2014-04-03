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

class DataGridEmbeddedTile extends AbstractTile{

    protected $dataGridOutputState;
    protected $gridConfJS;

    public function __construct(DataGridOutputState $dataGridOutputState, $gridConfJS) {
        $this->dataGridOutputState = $dataGridOutputState;
        $this->gridConfJS = $gridConfJS;
    }

    public function getViewParameters()
    {
        return array('gridState'=>$this->dataGridOutputState, 'gridConfJS'=>$this->gridConfJS, 'embedded'=>true);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridembedded.html.twig';
    }

    public function getName()
    {
        return 'datagridembedded';
    }
}