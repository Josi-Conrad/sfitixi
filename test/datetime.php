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

//Timezones
$local = new \DateTime();
echo "\n\nDateTime Now\n".$local->format('d.m.Y H:i')."\n";

$local->setTimezone(new DateTimeZone('Europe/Zurich'));
echo "Zurich \n".$local->format('d.m.Y H:i')."\n";

$utc = new \DateTime('now', new DateTimeZone('UTC'));
echo "UTC \n".$utc->format('d.m.Y H:i')."\n";

$local->setTimezone(new DateTimeZone('UTC'));
echo "Zurich converted to UTC \n".$local->format('d.m.Y H:i')."\n";

$utc->setTimezone(new DateTimeZone('Europe/Zurich'));
echo "UTC converted to Zurich \n".$utc->format('d.m.Y H:i')."\n";