<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 20:41
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class FormTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $isStandalone;

    public function __construct($formId, $form, $isStandalone=false) {
        $this->formId = $formId;
        $this->form = $form;
        $this->isStandalone = $isStandalone;
        $this->add(new FormControlTile($formId));
    }

    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'isStandalone'=>$this->isStandalone, 'form'=>$this->form->createView());
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:form.html.twig';
    }

    public function getName()
    {
        return 'form';
    }
}