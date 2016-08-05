<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 5-8-2016
 * Time: 16:43
 */
class KingdomHelper
{
    public static function random_location() {
        return rand(0,100) . ":" . rand(0,100);
    }

    public static function get_username_at_location($location) {
        if (strrpos($location, ":") == false) return false;

        $cloc = explode(":", preg_replace('/[^0-9\:]/m', '', $location));
        if (count($cloc) != 2) return false;
        $cloc[0] = intval($cloc[0]);
        $cloc[1] = intval($cloc[1]);

        $l = $cloc[0] . ":" . $cloc[1];

        // cases:	,xx:yy,
        //		xx:yy,
        //		,xx:yy
        //		xx:yy

        $taken = DBCommunicator::getInstance()->executeQuery("SELECT username FROM kingdom WHERE locations LIKE \"%," . $l . ",%\" OR locations LIKE \"" . $l . ",%\" OR locations LIKE \"%," . $l . "\"  OR locations = \"" . $l . "\";");
        if ($taken !== false) return $taken["username"];
        return false;

    }
}