<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.02.14
 * Time: 18:03
 */

namespace Tixi\ApiBundle\Interfaces;

use Symfony\Component\Validator\Constraints as Assert;

class POIRegisterDTO extends AddressRegisterDTO {
    //POI
    public $id;
    public $isActive;
    public $name;
    public $department;
    public $telephone;
    public $comment;
    public $memo;
    public $details;

    //POIKeywords
    public $keywords;
}