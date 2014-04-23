<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 00:48
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class SubmitButtonTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class SubmitButtonTile extends AbstractTile{

    public static $primaryType = 'primary';
    public static $defaultType = 'default';

    protected $formId;
    protected $displayText;
    protected $type;

    /**
     * @param $formId
     * @param $displayText
     * @param null $type
     */
    public function __construct($formId, $displayText, $type=null) {
        $this->type = (null !== $type) ? $type : self::$defaultType;
        $this->formId = $formId;
        $this->displayText = $displayText;
    }

    /**
     * @return array
     */
    public function getViewParameters() {
        return array('formId'=>$this->formId,'displayText'=>$this->displayText, 'type'=>$this->type);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:submitbutton.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'submitbutton';
    }
}