<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once "DBCommunicator.php";
require_once "Command.php";
require_once "Communicators/DiscordCommunicator.php";
require_once "Helpers/KingdomHelper.php";
foreach (glob("Commands/*.php") as $filename)
{
    require_once $filename;
}

foreach (glob("Commands/Trade/*.php") as $filename)
{
    require_once $filename;
}

require_once "CommandEvaluator.php";
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 17:28
 */

class Kingdom {
    public $channel;
    public $glochannel;
    public $lastturn;

    /** @var CommandEvaluator */
    private $_commandEvaluator;
    /** @var DBCommunicator */
    private $_db;
    private $_kingdom;
    /** @var Communicator */
    private $_communicator;
    public function __construct(DiscordMessage $input, $channel, $glochannel) {
        $db = DBCommunicator::getInstance();
        $kingdom = $db->getKingdom($input->getAuthorName());
        $this->_db = $db;
        $this->channel = $channel;
        $this->glochannel = $glochannel;
        $this->_kingdom = $kingdom;
        $communicator = new DiscordCommunicator($channel, $glochannel);
        $this->_communicator = $communicator;
        $this->_commandEvaluator = new CommandEvaluator($kingdom, $communicator);
    }


    public function translate($c) {

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

    public function print_kingdom($d) {

        $loc = $this->make_loc_array($d['locations']);

        if (count($loc) <= 4) {
            $locs = implode(", ", $loc);
        } else {
            $locs = $loc[0] . ", " . $loc[1] . " and " . (count($loc) - 2) . " other locations";
        }

        if ($loc > 10)
            $m =  $d['username'] . "'s kingdom is at " . $locs . " and has: " . round($this->calculate_attack_rating($d)) . " attack rating and " . round($this->calculate_defence_rating($d)) . " defence rating, ";

        if ($d['locations'] == "") return $d['username'] . "'s kingdom is a smouldering ruin";

        $a = array();

        foreach ($d as $k => $v) {
            if ($k != "username" && $k != "locations")
                $a[] = intval($v) . " " . strtolower($this->translate($k))  ;
        }

        return $m . implode(",  ", $a);
    }


    /***
    CORE VALUES:
    L	= 	LAND (acres)
    G	= 	GOLD (gp)
    F	=	FOOD (bushels)
    W	=	WEAPONS (number)
    S	=	SOLDIERS
    M	=	MAGIC
    I	=	IRON
    P	=	PEOPLE

    R	=	ROCK (STONE)
    HO	=	HORSES
    WO	=	WOOD
    WA	=	WATER


    BUILDINGS:
    B	=	BANKS			[ LU =  2	C =  10 ]
    FA	=	FARMS			[ LU = 10	C =  50 ]
    MN	=	MINES			[ LU =  5	C = 100 ]
    FR	=	FORESTS			[ LU = 10	C =  30 ]
    SM	=	SMELTERS		[ LU =  2	C =  20 ]
    BT	=	BATTLEMENTS		[ LU =  1 	C =  25 ]
    U	=	military school 		[ LU = 10 	C =  50 ]
    PR	=	PREISTHOODS		[ LU =  5 	C =  60 ]
    WF	=	WEAPONS	FACTORY 	[ LU =  2 	C =  50 ]
    BK	=	BARRACKS		[ LU =  5 	C = 100 ]
    TC	=	TRADE	CENTER		[ LU =  1 	C =  10 ]
    H	=	HOUSING			[ LU =  5 	C =  40 ]
    T	=	THEIVES	DEN		[ LU =  1 	C =  20 ]
    IA	=	INTELLIGENCE AGENCY 	[ LU =  1 	C = 100 ]

    D	=	DAMS
    ST	=	STABLES
    Q	=	QUARRY

    ROUND INFO:
    SG	= 	START GOLD
    SI	=	START IRON
    SF	=	START FOOD
    SP	=	START POPULATION
    SS	=	START SOLDIERS

    RR	=	ROUND RATE (10 minutes)
    GPR	=	ROUND BASE GOLD RATE
    GPB	= 	ROUND GOLD PER BANK
    FPF	=	ROUND FOOD PER FARM
    FPP	=	ROUND FOOD CONSUMED PER HEAD POPULATION

    IPM	=	ROUND IRON PER MINE
    WPF	=	ROUND WEAPONS PER FACTORY
    SPB	=	ROUND SOLDIERS PER BARRACKS

    LPK	=	LAND PER KINGDOM = 200
    CTA	=	COST TO ANNEX = 500

    SPELL INFO:
    DROUGHT	=	rand(30,70) food production
    RAIN		=	rand(100, 130) food production
    plague		=	rand(0,10) population reduction
    FIRE		=	rand(0, 10) razing of structures - negated by rain
    HEAL		=	negate plague
    PICKPOCKET	=	rand(0, 10) easier to steal from

     ***/



    public static $buildings;
    public static $buildings_key;
    public static $buildings_lookup;

    public static $spells;


    public function raze($u, $b, $a) {
        //execute raze command
    }


    // user, buildingid, amount, returns a message about the build
    public function build($u, $b, $a) {
        //execute build command
    }


    public function buildmax($u, $b) {
        //execute buildmax command
    }


    public function autoannex($u) {
        //execute auto annex command
    }


    public function find_nearest_tile_of_user($u, $loca) {
        $u = clean($u);

        $k = $this->_db->getKingdom($u);

        $locations = $this->make_loc_array($k['locations']);

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

    public function obliterate($u, $cloc) {
        //execute obliterate command
    }

    public function annex($u, $cloc) {
        //execute annex command
    }

    public function save_kingdom($k) {
        $updates = array();
        foreach($k as $key => $v) {

            if ($key != "username" && $key != "locations"){
                if ($v < 0) $v = 0;
                $updates[] = $key . "=" . round($v);
            }
        }

        $updates[] = "locations = \"" . implode(",", array_unique($this->make_loc_array(clean($k['locations'])))) . "\"";

        $updateq = "UPDATE kingdom SET " . implode(", ", $updates) . " WHERE username = \"" . clean($k['username']) . "\" LIMIT 1;";
        $this->_db->executeQuery($updateq);

    }

    public function turn() {
        //execute turn kingdom command
    }

    public function calculate_defence_rating($k) {
        //def = BT * S * (S/W)
        $horsessoldier = 1 + ($k['HO'] / ($k['S'] + 1));

        return $k['BT'] * ($horsessoldier * $k['S']) * ($k['S']/($k['W'] + 1));
    }

    public function calculate_attack_rating($k) {
        $weaponspersoldier = $k['W'] / ($k['S'] + 1);
        if ($weaponspersoldier > 1) $weaponspersoldier = 1;
        $horsessoldier = $k['HO'] / ($k['S'] + 1);

        $militaryschoolperbarracks = $k['U'] / ($k['BK'] + 1);

        $effectiveness = $weaponspersoldier + $horsessoldier + ($militaryschoolperbarracks / 20);

        return pow($k['S'] * $effectiveness * (($k['SI'] + 2)/2), ($k['WM'] + 1)) ;

    }

    public function make_loc_array($locstring) {
        $loc = array();
        if (strrpos($locstring, ',') === false) {
            $loc[] = $locstring;
        } else {
            $loc = explode(',' , $locstring);
        }
        return $loc;

    }

    public function attack($loc, $attacker) {
        //attack command
    }

    public function calculate_prices($k, $selling) {

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
            "food" => round((self::$buildings['FA']['g'] / 8.5) * (1 + $foodmarkup) * $discount, 2),
            "wood" => round((self::$buildings['FR']['g'] / 10) * (1 + $woodmarkup) * $discount, 2),
            "water" => round((self::$buildings['D']['g'] / 200) * (1 + $watermarkup) * $discount, 2),
            "stone" => round((self::$buildings['Q']['g'] / 5) * (1 + $stonemarkup) * $discount, 2),
            "soldiers" => round((self::$buildings['BK']['g'] / 3) * (1 + $soldiermarkup) * $discount, 2)

            //		"weapons" => round((self::$buildings['WF']['g'] / 10) * (1 + $weaponsmarkup) * $discount, 2),
            //		"runes" => round((self::$buildings['PR']['g'] / 10) * (1 + $magicmarkup) * $discount, 2),
            //		"iron" => round((self::$buildings['MN']['g'] / 7) * (1 + $ironmarkup) * $discount, 2),
            //		"people" => round((self::$buildings['H']['g'] / 3) * (1 + $peoplemarkup) * $discount, 2)
        );
    }

    public function item_translate($i) {
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

    public function trade($u, $bs, $amount, $item) {
        //execute trade command
    }


    public function buymax($u, $item) {
        //execute buy max command
    }

    public function sellall($u, $item) {
       //execute sell all command
    }

    public function add_turn_note($from, $to, $note) {
        $from = clean($from);
        $to = clean($to);
        $note = clean_note($note);
        $this->_db->executeQuery("INSERT INTO turnnotes (fromuser, touser, notes) VALUES (\"" . clean($from) . "\", \"" . clean($to) . "\", \"" . clean_note($note) . "\") ON DUPLICATE KEY UPDATE notes = CONCAT(notes, \"\\n\", \"" . clean_note($note) . "\");");
    }

    public function resolve_location_from_input($loc) {
        if (strrpos($loc, ":") !== false) {
            // location
            $player = $this->get_username_at_location($loc);
            if (!$player) return false;
        } else {
            $player = $this->_db->getKingdom($loc);
            if ($player === false) return false;
            if (strrpos($player['locations'], ",") !== false) {
                $loc = explode(",",$player['locations']);
                $loc = $loc[0];
            } else $loc = $player['locations'];
            if ($loc == "") return false;
        }
        return $loc;
    }


    public function get_active_spells($u) {

        $u = clean($u);
        $result = $this->_db->executeQuery("SELECT * FROM spells WHERE caston = \"" . clean($u) . "\";");

        $activespells = array();
        if ($result->num_rows > 0) {
            while($spells = $result->fetch_assoc()) {
                if (!isset($activespells[$spells['spell']])) $activespells[$spells['spell']] = 0;
                $activespells[$spells['spell']] += 1;
            }
        }

        return $activespells;
    }

    public function turn_remove_old_spells() {
        $this->_db->executeQuery("UPDATE spells SET duration = duration - 1;");
        $this->_db->executeQuery("DELETE FROM spells WHERE duration <= 0;");
    }

    public function  cast($spell, $victim, $attacker) {
        //execute cast spell
    }

    public function espionage($loc, $u) {
        //execute spy command
    }

    public function thieve($loc, $u) {
       //execute steal command
    }

    public function yolo($u) {
        // execute yolo command
    }

    public function processCommand(DiscordMessage $message) {
        $this->_commandEvaluator->evaluateCommand($message);
    }

}