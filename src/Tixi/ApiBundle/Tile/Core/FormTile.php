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

    protected $headerDisplayName;
    protected $form;

    public function __construct($headerDipslayName , $form) {
        $this->form = $form;
    }

    public function getViewParameters()
    {
        return array('form'=>$this->form->createView());
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:form.html.twig';
    }

    public function getName()
    {
        return 'formTile';
    }
}