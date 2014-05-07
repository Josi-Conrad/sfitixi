<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 07.05.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Helper;

/**
 * Class WeekdayService
 * @package Tixi\ApiBundle\Helper
 */
class WeekdayService {

    /**
     * @var array
     * php dayname -> ISO-8601 numeric representation of the day of the week
     */
    public static $weekdayToNumericConverter = array(
        'monday'=>1,
        'tuesday'=>2,
        'wednesday'=>3,
        'thursday'=>4,
        'friday'=>5,
        'saturday'=>6,
        'sunday'=>7,
    );

    /**
     * @var array
     * ISO-8601 numeric representation of the day of the week -> php dayname
     */
    public static $numericToWeekdayConverter = array(
        1=>'monday',
        2=>'tuesday',
        3=>'wednesday',
        4=>'thursday',
        5=>'friday',
        6=>'saturday',
        7=>'sunday'
    );

} 