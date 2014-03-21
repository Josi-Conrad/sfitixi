<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 04.03.14
 * Time: 00:31
 */

namespace Tixi\ApiBundle\Interfaces;

use JMS\Serializer\Annotation\SerializedName;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\DataGridField;
use Tixi\ApiBundle\Shared\DataGrid\Annotations\DataGridRowId;
use Tixi\ApiBundle\Shared\DataGrid\DataGridSourceClass;


class VehicleListDTO implements DataGridSourceClass{
    /**
     * @DataGridRowId()
     */
    public $id;
    /**
     * @DataGridField(headerName="Name", order=1)
     */
    public $name;
    /**
     * @SerializedName("licenceNumber")
     * @DataGridField(headerName="Lizenznummer", order=2)
     */
    public $licenceNumber;
    /**
     * @DataGridField(headerName="Inverkehrssetzung", order=5)
     */
    public $dateOfFirstRegistration;
    /**
     * @DataGridField(headerName="Parkplatz Nummer", order=3)
     */
    public $parkingLot;
    /**
     * @DataGridField(headerName="Fahrzeugkategorie", order=4)
     */
    public $category;
} 