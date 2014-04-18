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
 * Class DataGridEmbeddedTile
 * @package Tixi\ApiBundle\Shared\DataGrid\Tile
 */
class DataGridEmbeddedTile extends AbstractTile{

    protected $dataGridOutputState;
    protected $gridConfJS;
    protected $gridId;

    /**
     * @param DataGridOutputState $dataGridOutputState
     * @param $gridConfJS
     * @param $gridId
     */
    public function __construct(DataGridOutputState $dataGridOutputState, $gridConfJS, $gridId) {
        $this->dataGridOutputState = $dataGridOutputState;
        $this->gridConfJS = $gridConfJS;
        $this->gridId = $gridId;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('gridId'=>$this->gridId, 'gridState'=>$this->dataGridOutputState, 'gridConfJS'=>$this->gridConfJS, 'embedded'=>true);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:datagridembedded.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'datagridembedded';
    }
}