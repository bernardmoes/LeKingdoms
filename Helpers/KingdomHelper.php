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

        $taken = DBCommunicator::getInstance()->executeQuery("SELECT username FROM kingdom WHERE locations LIKE \"%," . $l . ",%\" OR locations LIKE \"" . $l . ",%\" OR locations LIKE \"%," . $l . "\"  OR locations = \"" . $l . "\";")->fetch(PDO::FETCH_ASSOC);
        if ($taken !== false) return $taken["username"];
        return false;

    }

    public static function turn_remove_old_spells() {
        DBCommunicator::getInstance()->executeQuery("UPDATE spells SET duration = duration - 1;");
        DBCommunicator::getInstance()->executeQuery("DELETE FROM spells WHERE duration <= 0;");
    }

    public static function get_active_spells($u) {

        $u = clean($u);
        $result = DBCommunicator::getInstance()->executeQuery("SELECT * FROM spells WHERE caston = \"" . clean($u) . "\";")->fetchAll(PDO::FETCH_ASSOC);

        $activespells = array();
        if (count($result) > 0) {
            foreach($result as $spells)
            {
                if (!isset($activespells[$spells['spell']])) $activespells[$spells['spell']] = 0;
                $activespells[$spells['spell']] += 1;
            }
        }

        return $activespells;
    }

    public static function make_loc_array($locstring) {
        $loc = array();
        if (strrpos($locstring, ',') === false) {
            $loc[] = $locstring;
        } else {
            $loc = explode(',' , $locstring);
        }
        return $loc;
    }

    public static function resolve_location_from_input($loc) {
        if (strrpos($loc, ":") !== false) {
            // location
            $player = KingdomHelper::get_username_at_location($loc);
            if (!$player) return false;
        } else {
            $player = DBCommunicator::getInstance()->getKingdom($loc);
            if ($player === false) return false;
            if (strrpos($player['locations'], ",") !== false) {
                $loc = explode(",",$player['locations']);
                $loc = $loc[0];
            } else $loc = $player['locations'];
            if ($loc == "") return false;
        }
        return $loc;
    }

    public static function add_turn_note($from, $to, $note)
    {
        $from = clean($from);
        $to = clean($to);
        $note = clean_note($note);
        DBCommunicator::getInstance()->executeQuery("INSERT INTO turnnotes (fromuser, touser, notes) VALUES (\"" . clean($from) . "\", \"" . clean($to) . "\", \"" . clean_note($note) . "\") ON DUPLICATE KEY UPDATE notes = CONCAT(notes, \"\\n\", \"" . clean_note($note) . "\");");
    }
}