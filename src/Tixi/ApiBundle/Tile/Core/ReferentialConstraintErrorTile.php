<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.04.14
 * Time: 16:09
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class ReferentialConstraintErrorTile extends AbstractTile{

    protected $amountOfObjects;

    public function __construct($amountOfObjects) {
        $this->add(new BackButtonTile());
        $this->amountOfObjects = $amountOfObjects;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('amountOfObjects'=>$this->amountOfObjects);
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:referentialintegrityerror.html.twig';
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return 'referintialintegrityerror';
    }
}