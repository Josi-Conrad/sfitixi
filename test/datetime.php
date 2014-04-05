<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 04.04.14
 * Time: 23:26
 */

$date = DateTime::createFromFormat('d.m.Y','06.05.2014');

$mai = DateTime::createFromFormat('d.m.Y','01.06.2014');

//echo $mai->format('d.m.Y');

echo date('d.m.Y', strtotime('last Saturday',$mai->getTimestamp()));