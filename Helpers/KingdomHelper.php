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

    public static $spells = array (
        "drought" => array("r" => 50, "l" => 1, "d" => "decreases the productivity of enemy farms"),
        "rain" => array("r" => 10, "l" => 1, "d" => "increases the productivity of farms and forests and decreases damage from fires"),
        "plague" => array("r" => 50, "l" => 1, "d" => "kills a random percentage of the enemy population"),
        "fire" => array("r" => 50, "l" => 1, "d" => "razes a random number of enemy buildings to the ground"),
        /*		"shield" => array("r" => 300, "l" => 1, "d" => "defends against attacks for one round"),*/
        "protection" => array("r" => 3000, "l" => 3, "d" => "defends against attacks for three rounds"),
        "health" => array("r" => 20, "l" => 2, "d" => "defends against plagues"),
        "wardance" => array("r" => 3000, "l" => 1, "d" => "attacks on the target can capture up to 10 squares of land")
    );

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

    public static function translate($c) {
        if ($c == "L") return 'free land';
        if ($c == "G") return 'gc';
        if ($c == "F") return 'bushels of food';
        if ($c == "W") return 'weapons';
        if ($c == "S") return 'soldiers';
        if ($c == "M") return 'magical runes';
        if ($c == "B") return 'banks';
        if ($c == "FA") return 'farms';
        if ($c == "MN") return 'mines';
        if ($c == "FR") return 'forests';
        if ($c == "SM") return 'smelters';
        if ($c == "BT") return 'battlements';
        if ($c == "U") return 'military school';
        if ($c == "PR") return 'priesthoods';
        if ($c == "WF") return 'weapons factories';
        if ($c == "BK") return 'barracks';
        if ($c == "TC") return 'trade centers';
        if ($c == "H") return 'houses';
        if ($c == "T") return 'theives dens';
        if ($c == "IA") return 'intelligence agencies';
        if ($c == "I") return 'bars of iron';
        if ($c == "P") return 'people';
        if ($c == "D") return 'dams';
        if ($c == "ST") return 'stables';
        if ($c == "Q") return 'quarries';
        if ($c == "TI") return 'technical institutes';
        if ($c == 'WO') return 'faggots of wood';
        if ($c == 'HO') return 'horses';
        if ($c == 'WA') return 'liters of water';
        if ($c == 'R') return 'tons of stone';
        if ($c == 'SI') return 'siege towers';
        if ($c == 'WM') return 'war machines';
    }

    public static function item_translate($i) {
        if ($i == 'food') return 'F';
        if ($i == 'weapons') return 'W';
        if ($i == 'soldiers') return 'S';
        if ($i == 'runes') return 'M';
        if ($i == 'iron') return 'I';
        if ($i == 'people') return 'P';
        if ($i == 'wood') return 'WO';
        if ($i == 'horses') return 'HO';
        if ($i == 'water') return 'WA';
        if ($i == 'stone') return R;
        return false;
    }

    public static function calculate_prices($k, $selling) {

        // optimal number of trade centers would be 20.

        $discount = 1 + ( ($selling == true ? -1 : -1) * (0.25 * (($k['TC'] <= 20 ? $k['TC'] : 20) / 20)));

        //	$weaponsmarkup =  1 - ( ($k['W'] <= (20 + $k['S']) ? $k['W'] : (20 +  $k['S']))  / (20 + $k['S']) );
        //	$peoplemarkup =  1 - ( ($k['P'] <= 50 ? $k['P'] : 20)  / 20 );
        //	$magicmarkup =  1 - ( ($k['M'] <= 15 ? $k['M'] : 15)  / 15 );
        //	$ironmarkup =  1 - ( ($k['I'] <= 50 ? $k['I'] : 50)  / 50 );
        //	$horsesmarkup =  1 - ( ($k['HO'] <= 10 ? $k['HO'] : 10)  / 10 );


        $foodmarkup =  1 - ( ($k['F'] <= 50 ? $k['F'] : 50)  / 50 );

        $soldiermarkup =  1 - ( ($k['S'] <= 50 ? $k['S'] : 50)  / 50 );

        $woodmarkup =  1 - ( ($k['WO'] <= 100 ? $k['WO'] : 100)  / 100 );

        $watermarkup =  1 - ( ($k['WA'] <= 100 ? $k['WA'] : 100)  / 100 );

        $stonemarkup =  1 - ( ($k['R'] <= 100 ? $k['R'] : 100)  / 100 );


        return array (
            "food" => round((KingdomHelper::$buildings['FA']['g'] / 8.5) * (1 + $foodmarkup) * $discount, 2),
            "wood" => round((KingdomHelper::$buildings['FR']['g'] / 10) * (1 + $woodmarkup) * $discount, 2),
            "water" => round((KingdomHelper::$buildings['D']['g'] / 200) * (1 + $watermarkup) * $discount, 2),
            "stone" => round((KingdomHelper::$buildings['Q']['g'] / 5) * (1 + $stonemarkup) * $discount, 2),
            "soldiers" => round((KingdomHelper::$buildings['BK']['g'] / 3) * (1 + $soldiermarkup) * $discount, 2)

            //		"weapons" => round((self::$buildings['WF']['g'] / 10) * (1 + $weaponsmarkup) * $discount, 2),
            //		"runes" => round((self::$buildings['PR']['g'] / 10) * (1 + $magicmarkup) * $discount, 2),
            //		"iron" => round((self::$buildings['MN']['g'] / 7) * (1 + $ironmarkup) * $discount, 2),
            //		"people" => round((self::$buildings['H']['g'] / 3) * (1 + $peoplemarkup) * $discount, 2)
        );
    }

    public static $buildings_key =
        array(
            "B" => "bank",
            "FA" => "farm",
            "MN" => "mine",
            "FR" => "forest",
            "SM" => "smelter",
            "BT" => "battlements",
            "U" => "military school",
            "PR" => "priesthood",
            "WF" => "weapons factory",
            "BK" => "barracks",
            "TC" => "trade center",
            "H" => "house",
            "T" =>	"thieves den",
            "IA" => "intelligence agency",
            "D" => "dam",
            "ST" => "stable",
            "Q" => "quarry",
            "TI" => "technical institute",
            "SI" => "siege tower",
            "WM" => "war machine"
        );

    public static $buildings = array(
        "B" => 	array("wo" => 10, "r" => 0, "i"=> 10,  "l" => 2,	"g" => 100,	"d" => "generates income"),
        "FA" => array("wo" => 10, "r" => 0, "i" => 0, "l" => 10,	"g" => 50,	"d" => "generates food"),
        "MN" => array("wo" => 5, "r" => 0, "i" => 0, "l" => 5,	"g" => 100,	"d" => "generates iron"),
        "FR" => array("wo" => 0, "r" => 0, "i" => 0, "l" => 10,	"g" => 30,	"d" => "generates wood, and some food"),
        "SM" => array("wo" => 10, "r" => 10, "i" => 0, "l" => 2,	"g" => 20,	"d" => "boosts mining, weapons factories"),
        "BT" => array("wo" => 20, "r" => 20, "i" => 10, "l" => 2,	 "g" => 25,	"d" => "increases the kingdom's defences"),
        "U" => 	array("wo" => 20, "r" => 0, "i" => 0, "l" => 10,	 "g" => 50,	"d" => "increases your army's prowess"),
        "PR" => array("wo" => 20, "r" => 0, "i" => 0, "l" => 5,	 "g" => 60,	"d" => "generates magical runes"),
        "WF" =>	array("wo" => 10, "r" => 0, "i" => 0, "l" => 2,	 "g" => 50,	"d" => "generates weapons from iron"),
        "BK" => array("wo" => 10, "r" => 0, "i" => 0, "l" => 5,	 "g" => 100,	"d" => "houses and trains soldiers"),
        "TC" =>	array("wo" => 40, "r" => 0, "i" => 10, "l" => 20, 	"g" => 50,	"d" => "allows you to !trade commodities"),
        "H" =>	array("wo" => 5, "r" => 0, "i" => 0, "l" => 5, 	"g" => 40,	"d" => "generates population"),
        "T" =>	array("wo" => 5, "r" => 0, "i" => 0, "l" => 1, 	"g" => 20,	"d" => "allows you to thieve from other kingdoms"),
        "IA" => array("wo" => 20, "r" => 5, "i" => 0, "l" => 2, "g" => 100, "d" => "gathers information on other kingdoms"),
        "D" => array("wo" => 5, "r" => 20, "i" => 0, "l" => 30, "g" => 100, "d" => "creates water for your population"),
        "ST" => array("wo" => 15, "r" => 5, "i" => 0, "l" => 20, "g" => 75, "d" => "generates and stables horses"),
        "Q" => array("wo" => 50, "r" => 0, "i" => 0, "l" => 15, "g" => 40, "d" => "generates stone"),
        "TI" => array("wo" => 50, "r" => 5, "i" => 5, "l" => 10, "g" => 50, "d" => "increases space efficiency"),
        "SI" => array("wo" => 5000, "r" => 100, "i" => 1000, "l" => 5, "g" => 5000, "d" => "advanced attack unit, increases attack rating"),
        "WM" => array("wo" => 32767, "r" => 32767, "i" => 32767, "l" => 1000, "g" => 50000000, "d" => "war machine... for obliterating opponents")
    );

    public static function printKingdom($d) {

        $loc = KingdomHelper::make_loc_array($d['locations']);

        if (count($loc) <= 4) {
            $locs = implode(", ", $loc);
        } else {
            $locs = $loc[0] . ", " . $loc[1] . " and " . (count($loc) - 2) . " other locations";
        }

        if ($loc > 10)
            $m =  $d['username'] . "'s kingdom is at " . $locs . " and has: " . round(KingdomHelper::calculateAttackRating($d)) . " attack rating and " . round(KingdomHelper::calculateDefenceRating($d)) . " defence rating, ";

        if ($d['locations'] == "") return $d['username'] . "'s kingdom is a smouldering ruin";

        $a = array();

        foreach ($d as $k => $v) {
            if ($k != "username" && $k != "locations")
                $a[] = intval($v) . " " . strtolower(KingdomHelper::translate($k))  ;
        }

        return $m . implode(",  ", $a);
    }

    public static function calculateAttackRating($k) {
        $weaponspersoldier = $k['W'] / ($k['S'] + 1);
        if ($weaponspersoldier > 1) $weaponspersoldier = 1;
        $horsessoldier = $k['HO'] / ($k['S'] + 1);

        $militaryschoolperbarracks = $k['U'] / ($k['BK'] + 1);

        $effectiveness = $weaponspersoldier + $horsessoldier + ($militaryschoolperbarracks / 20);

        return pow($k['S'] * $effectiveness * (($k['SI'] + 2)/2), ($k['WM'] + 1)) ;

    }

    public static function calculateDefenceRating($k) {
        $horsessoldier = 1 + ($k['HO'] / ($k['S'] + 1));
        return $k['BT'] * ($horsessoldier * $k['S']) * ($k['S']/($k['W'] + 1));
    }

    public static function find_nearest_tile_of_user($u, $loca) {
        $u = clean($u);

        $k = DBCommunicator::getInstance()->getKingdom($u);

        $locations = KingdomHelper::make_loc_array($k['locations']);

        if (!$loca) return false;

        $looknear = explode(":", $loca);
        $lx = intval($looknear[0]);
        $ly = intval($looknear[1]);

        $smallestdist = 1000;
        $smallestloc = false;

        foreach($locations as $k => $l) {
            $loc = explode(":", $l);

            $x = intval($loc[0]);
            $y = intval($loc[1]);

            $dist = pow(pow($lx - $x, 2) + pow($ly - $y, 2), 0.5);

            if ($dist < $smallestdist) {
                $smallestdist = $dist;
                $smallestloc = $l;
            }
        }
        return $smallestloc;
    }

    public static function annex($u, $cloc)
    {
        $cloc =  explode(":", preg_replace('/[^0-9:]+/sm', '', $cloc));
        $u = clean($u);
        $d = DBCommunicator::getInstance()->getKingdom($u);

        if (count($cloc) != 2) return "please use the command like this !annex number:number";

        $cloc[0] = intval($cloc[0]);
        $cloc[1] = intval($cloc[1]);

        //		$this->room($u . ' is trying to annex ' . $cloc[0] . ":" . $cloc[1]);

        if ($cloc[0] < 0 || $cloc[0] > MAP_SIZE || $cloc[1] < 0 || $cloc[1] > MAP_SIZE) return "the specified land is outside the bounds of the world, sorry.";


        $locations = KingdomHelper::make_loc_array($d['locations']); //explode(",", $d['locations']);

        if ($d['P']  / (count($locations) + 1)  < PEOPLE_PER_LANDSQUARE_FOR_ANNEXATION) {
            $popneeded = (count($locations) + 1) * PEOPLE_PER_LANDSQUARE_FOR_ANNEXATION - $d['P'];
            return "you cannot annex new land until you have sufficient people to do so. " . $popneeded . " more people needed!";
        }

        $canannex = false;

        foreach($locations as $k => $l) {

            $coord = explode(':', $l);

            if ($cloc[0] == $coord[0] && $cloc[1] == $coord[1]) return "you cannot annex land that you already own! (" . $coord[0] . ":" . $coord[1] . ")";

            if (($coord[0] + 1  == $cloc[0] || $coord[0] -1 == $cloc[0] ) && ($coord[1] + 1 == $cloc[1] || $coord[1] -1 == $cloc[1] )){
                $canannex = true;
                break;
            }
            if (($coord[0]  == $cloc[0] ) && ($coord[1] + 1 == $cloc[1] || $coord[1] -1 == $cloc[1] )){
                $canannex = true;
                break;
            }

            if (($coord[1]  == $cloc[1] ) && ($coord[0] + 1 == $cloc[0] || $coord[0] -1 == $cloc[0] )){
                $canannex = true;
                break;
            }


        }


        if (!$canannex) return "you cannot annex a land that is not adjacent to your own";

        if ($canannex) {
            // check if already taken!
            $taken = KingdomHelper::get_username_at_location($cloc[0] . ":" . $cloc[1]);
            if ($taken !== false) return "that land is already owned by " . $taken;
        }

        if ($d['G'] < COST_TO_ANNEX) return "you have insufficent gc to perform surveying of this site. the cost is " . COST_TO_ANNEX . " gc";

        // got to here, so annex away

        $gold = $d['G'] - COST_TO_ANNEX;
        $land = $d['L'] + LAND_PER_KINGDOM;
        $locations = $d['locations'] . "," . $cloc[0] . ":" . $cloc[1];
        DBCommunicator::getInstance()->executeQuery("UPDATE kingdom SET G=" . intval($gold) . ", L=" . intval($land) . ", locations=\"" . $locations . "\" WHERE username =\"" . clean($u) . "\" LIMIT 1;");

        return "land at " . $cloc[0] . ":" . $cloc[1] .  " annexed!";
    }

    public static $buildings_lookup;

}