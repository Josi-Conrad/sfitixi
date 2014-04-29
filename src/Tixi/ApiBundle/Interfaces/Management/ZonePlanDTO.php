<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 28.04.14
 * Time: 10:33
 */

namespace Tixi\ApiBundle\Interfaces\Management;

/**
 * Class ZonePlanDTO
 * No ID, there exists only 1 ZonePlan (ZonePlanManagement handles ID/Repo)
 * @package Tixi\ApiBundle\Interfaces\Management
 */
class ZonePlanDTO {
    public $innerZone;
    public $adjacentZone;
    public $normalTarif;
    public $innerTarif;
    public $adjacentTarif;
} 