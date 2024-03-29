<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.05.14
 * Time: 20:17
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

/**
 * Class DrivingOrderCreateTile
 * @package Tixi\ApiBundle\Tile\Dispo
 */
class DrivingOrderCreateTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $passengerId;
    protected $serviceUrls;

    /**
     * @param $form
     * @param $passengerId
     * @param $serviceUrls
     */
    public function __construct($form, $passengerId, $serviceUrls) {
        $this->formId = $form->getName();
        $this->form = $form;
        $this->passengerId = $passengerId;
        $this->serviceUrls = $serviceUrls;
        $this->add(new FormControlTile($this->formId));
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'form'=>$this->form->createView(), 'passengerId'=>$this->passengerId,
            'serviceUrls'=>$this->serviceUrls);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:drivingordercreate.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'drivingOrderCreateForm';
    }
} 