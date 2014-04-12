<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 11.04.14
 * Time: 14:14
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimeArrayTransformer
 * @package Tixi\ApiBundle\Form\Shared
 */
class DateTimeArrayTransformer implements DataTransformerInterface {
    public function transform($datetime) {
        if (null !== $datetime) {
            $date = clone $datetime;
            $time = clone $datetime;
        } else {
            $date = null;
            $time = null;
        }

        $result = array(
            'date' => $date,
            'time' => $time
        );

        return $result;
    }

    /**
     * @param mixed $array
     * @return mixed|null
     */
    public function reverseTransform($array) {
        $date = $array['date'];
        $time = $array['time'];

        if (null == $date || null == $time) {
            return null;
        }

        $date->setTime($time->format('H'), $time->format('i'));
        return $date;
    }
} 