<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 04.03.14
 * Time: 00:31
 */

namespace Tixi\ApiBundle\Interfaces;

use JMS\Serializer\Annotation\SerializedName;


class ServicePlanListDTO {
    public $id;
    /**
     * @SerializedName("startDate")
     */
    public $startDate;
    /**
     * @SerializedName("endDate")
     */
    public $endDate;
    /**
     * @SerializedName("memo")
     */
    public $memo;
}