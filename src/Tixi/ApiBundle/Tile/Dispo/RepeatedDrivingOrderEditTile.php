<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.06.14
 * Time: 02:24
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

/**
 * Class RepeatedDrivingOrderEditTile
 * @package Tixi\ApiBundle\Tile\Dispo
 */
class RepeatedDrivingOrderEditTile extends AbstractTile{

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
        return 'TixiApiBundle:Tile:repeateddrivingorderedit.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'repeatedDrivingOrderEditForm';
    }
} 