<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 20:41
 */

namespace Tixi\ApiBundle\Tile\Core;


use Symfony\Component\Form\Form;
use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class FormTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class FormTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $isStandalone;

    /**
     * @param $form Form
     * @param bool $isStandalone
     */
    public function __construct($form, $isStandalone=false) {
        $this->formId = $form->getName();
        $this->form = $form;
        $this->isStandalone = $isStandalone;
        $this->add(new FormControlTile($this->formId));
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'isStandalone'=>$this->isStandalone, 'form'=>$this->form->createView());
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:form.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'form';
    }
}