<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 00:48
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class SubmitButtonTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $formId;
    protected $displayText;
    protected $type;

    public function __construct($formId, $displayText, $type=null) {
        $this->type = (null !== $type) ? $type : self::$defaultType;
        $this->formId = $formId;
        $this->displayText = $displayText;
    }

    public function getViewParameters() {
        return array('formId'=>$this->formId,'displayText'=>$this->displayText, 'type'=>$this->type);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:submitbutton.html.twig';
    }

    public function getName()
    {
        return 'submitbutton';
    }
}