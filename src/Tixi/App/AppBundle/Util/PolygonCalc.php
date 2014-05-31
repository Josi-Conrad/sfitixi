<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 27.04.14
 * Time: 16:54
 */

namespace Tixi\App\AppBundle\ZonePlan;

/**
 * Includes several helping function to calc with points and polygons
 * Class PolygonCalc
 * @package Tixi\App\AppBundle\Util
 */
class PolygonCalc {
    /**
     * extracts Polygon points from geoJSON
     * see full specifiation from geoJSON here: http://geojson.org/geojson-spec.html#polygon
     * @param $json
     * @return array
     */
    public static function createPolygonFromGeoJSON($json) {
        $p = array();
        $j = json_decode($json);
        foreach ($j->features as $f) {
            if ($f->geometry->type == "Polygon")
                foreach ($f->geometry->coordinates as $coordinates) {
                    foreach($coordinates as $c){
                        array_push($p, new Point($c[1], $c[0]));
                    }
                }
        }
        return $p;
    }

    /**
     * give boolean if point is included in a polygon
     * see details here:
     * @param $p
     * @param $polygon
     * @return bool
     */
    public static function pointInPolygon($p, $polygon) {
        //time limit, if operates with too much points
        set_time_limit(60);

        $c = 0;
        $p1 = $polygon[0];
        $n = count($polygon);

        for ($i = 1; $i <= $n; $i++) {
            $p2 = $polygon[$i % $n];
            if ($p->lng > min($p1->lng, $p2->lng)
                && $p->lng <= max($p1->lng, $p2->lng)
                && $p->lat <= max($p1->lat, $p2->lat)
                && $p1->lng != $p2->lng
            ) {
                $xinters = ($p->lng - $p1->lng) * ($p2->lat - $p1->lat) / ($p2->lng - $p1->lng) + $p1->lat;
                if ($p1->lat == $p2->lat || $p->lat <= $xinters) {
                    $c++;
                }
            }
            $p1 = $p2;
        }

        // if the number of edges we passed through is even, then it's not in the poly.
        return $c % 2 != 0;
    }
} 