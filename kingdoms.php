<?php
$turnonly =  false;

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;



/****
 ***** KINGDOMS - A GAME BY SYTHE.ORG
 ****/
error_reporting(E_ALL);

function clean($input) {
	return preg_replace('/[^a-zA-Z0-9\#\-\:\,]/m', '', $input);

}
function clean_note($input) {
	return preg_replace('/[^a-zA-Z0-9\-\#\:\ \.\,]/m', '', $input);

}

class KingdomsGame {
	public $channel;
	public $glochannel;
	public $db;
	public $lastturn;
	public function pm($u, $m) {
		$this->channel->sendMessage("```@" . $u . ": " . $m . '```');
	}

	public function room($m) {
		$this->glochannel->sendMessage('```' . $m . '```');
	}

	public function both($u, $m) {
		$this->room( $m );
		if ($this->channel->channel_id == $this->glochannel->channel_id) return;
		$this->pm($u, $m);
	}

	public function reply($u, $ispm, $m) {
		$this->pm( $u, $m );
	}

	public function q($sql) {
	//	print($sql . "\n");
		$result = $this->db->query($sql);

		if($result && $result->num_rows > 0) {
			if($row = $result->fetch_assoc()) {
				return $row;
			}
		}
		return false;
	}

	public function random_location() {
		return rand(0,100) . ":" . rand(0,100);
	}


	public $discord;
	public function __construct($server, $username, $password, $dbname, $channel, $glochannel) {
		$this->channel = $channel;
		$this->glochannel = $glochannel;
		$this->db = new mysqli($server, $username, $password, $dbname) or die('couldnt connect');
		if (mysqli_connect_errno()) {
			//	printf("Connect failed: %s\n", mysqli_connect_error());
			//	$this->room("could not connect to le database. sorry");
			//	exit();
		}
	}	


	public function get_kingdom($u) {
		$d = $this->q(
				'SELECT * FROM kingdom WHERE username = "' . clean($u) . '";'
			     );
		return $d;
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

	public static $SG = 500; // start gold
	public static $SI = 20; // start iron
	public static $SF = 140; // start food
	public static $SP = 10; // start population
	public static $SS = 5; // start soldiers
	public static $SH = 5; // start houses
	public static $SFA = 2; // start farms 
	public static $SD = 1; // start dams 
	public static $SBT = 10; // starting battlements
	public static $SR = 10; // start stone
	public static $SW = 100; // start wood






	public static $LPK = 250; // land per kingdom
	public static $CTA = 500; // cost to annex
	public static $SIZE = 100; // map size 

	public static $GPR = 10; // round gold 
	public static $GPB = 10; // round gold per bank
	public static $FPF = 10; // food per farm
	public static $FPP = 1; // food consumed per head population
	public static $WPP = 10; // water consumed per head population
	public static $IPM = 5; // round iron per mine
	public static $WPF = 5; // round weapon per factory
	public static $SRR = 2; // round soldiers per barracks
	public static $MPP = 5; // magic per preisthood
	public static $PPH = 4; // people per house
	public static $PRR = 0.3; // population reproduction rate
	public static $SPB = 10; // soldiers per barracks
	public static $IPF = 1; // income per forest
	public static $FPR = 1; // food per forest
	public static $HTA = 10; // home turf advantage
	public static $WPD = 300; // water per dam
	public static $WDL = 1000; // water dam limit
	public static $SPQ = 2; // stone per quary
	public static $HPS = 5; // 5 horses to a stable
	public static $HRR = 1; // horse reproduction rate
	public static $TIA = 0.95; // new foodprint of entire kingdom per technical institute
	public static $WOF = 15; // wood per forest
	public static $PPLS = 20; // people per landsquare requirement for annexation

	public static $buildings;
	public static $buildings_key;
	public static $buildings_lookup;

	public static $spells;


	public function raze($u, $b, $a) {
		$b = preg_replace('/[^A-Z]+/sm', '', $b);
		$a = abs(intval($a));

		$d = $this->get_kingdom(clean($u));
		if ($d === false) return "user does not exist";
		if (!isset(self::$buildings[$b])) {
			if (!isset(self::$buildings[$b . 's'])) {

				return "building type does not exist (" . $b . ")";
			} else $b .= 's';
		}
		if ($a <= 0) $a = 1;

		if ($b == "TI") return "cannot raze technical institutes";

		$spacesaving = ( (self::$buildings[$b]['l'] * $a) * abs(1  - self::$TIA) * $d['TI'] );
		$land = (self::$buildings[$b]['l'] * $a);
		if ($spacesaving > 0.5 * $land) $spacesaving = 0.5 * $land;
		$land -= $spacesaving;


		if ($d[$b] < $a) return "not enough of that type of building exist to be destroyed. only " . $d[$b] . " exist";

		$d['L'] += $land;



		$d[$b] -= $a;

		$this->save_kingdom($d);

		return "your soldiers knock the foundations from under the " . $this->translate($b) . " wiping out a total of " . $a . " " . $this->translate($b) . " and freeing up " . $land . " acres of space";


	}


	// user, buildingid, amount, returns a message about the build
	public function build($u, $b, $a) {


		$b = preg_replace('/[^A-Z]+/sm', '', $b);
		$a = abs(intval($a));

		$d = $this->get_kingdom(clean($u));
		if ($d === false) return "user does not exist";
		if (!isset(self::$buildings[$b])) {
			if (!isset(self::$buildings[$b . 's'])) {

				return "building type does not exist (" . $b . ")";
			} else $b .= 's';
		}
		if ($a <= 0) $a = 1;

		$gold = (self::$buildings[$b]['g'] * $a);
		$spacesaving = ( (self::$buildings[$b]['l'] * $a) * abs(1  - self::$TIA) * $d['TI'] );

		$land = (self::$buildings[$b]['l'] * $a);

		if ($spacesaving > 0.5 * $land) $spacesaving = 0.5 * $land;
		$land -= $spacesaving;

		$rock = (self::$buildings[$b]['r'] * $a);
		$iron = (self::$buildings[$b]['i'] * $a);
		$wood = (self::$buildings[$b]['wo'] * $a);

		$report = "";

		if ($d['G'] < $gold) $report .= "not enough gold, needed at least " . $gold . " gc. ";
		if ($d['L'] < $land) $report .= "you do not have enough free space in your kingdom, needed at least " . $land . " acres. try !annex? ";
		if ($d['R'] < $rock) $report .= "not enough stone, needed at least " . $rock . " tons. ";
		if ($d['I'] < $iron) $report .= "not enough iron, needed at least " . $iron . " iron bars. ";
		if ($d['WO'] < $wood) $report .= "not enough wood, needed at least " . $wood . " faggots. ";

		if ($d[$b] + $a > 32767) $report .= "building this many " . $this->translate($b) . " would exceed the maximum allowable structures of 32767. ";

		if ($report != "") return $report;

		$report = array();

		$goldleft = $d['G'] - $gold;
		$landleft = $d['L'] -$land;
		$rockleft = $d['R'] - $rock;
		$ironleft = $d['I'] - $iron;
		$woodleft = $d['WO'] - $wood;

		if ($gold > 0) $report[] = "" .  $goldleft . " gc";
		if ($wood > 0) $report[] = "" . $woodleft . " faggots";
		if ($rock > 0) $report[] = "" . $rockleft . " tons of stone";
		if ($iron > 0) $report[] = "" . $ironleft . " iron bars";


		$rep = implode(", ", $report);
		$built = $d[$b] + $a;	

		//$this->q("UPDATE kingdom SET WO=" . intval($woodleft) . ", R=" . intval($rockleft) . ", I=" . intval($ironleft) . ", G=" . intval($goldleft) . ", L=" . intval($landleft) . ", " . clean($b) . "=" . clean($built) . " WHERE username = \"" . clean($u) . "\" LIMIT 1;");

		$d['G'] = $goldleft;
		$d['L'] = $landleft;
		$d['R'] = $rockleft;
		$d['I'] = $ironleft;
		$d['WO'] = $woodleft;
		$d[$b] = $built;

		$this->save_kingdom($d);

		return "built " . $a . " " . self::$buildings_key[$b] . ". you have " . $rep . " remaining";
	}


	public function buildmax($u, $b) {


		$b = preg_replace('/[^A-Z]+/sm', '', $b);
		$a = abs(intval($a));

		$d = $this->get_kingdom(clean($u));
		if ($d === false) return "user does not exist";
		if (!isset(self::$buildings[$b])) {
			if (!isset(self::$buildings[$b . 's'])) {

				return "building type does not exist (" . $b . ")";
			} else $b .= 's';
		}



		$amount = 32767 - $d[$b];

		if (self::$buildings[$b]['g'] > 0) {
			$tmp = $d['G'] / self::$buildings[$b]['g'];
			if ($tmp < $amount) $amount = $tmp;
		}


		if ($d['L'] == 0) {
			$amount = 0;
		} else {
			$spacesaving = ( (self::$buildings[$b]['l'] ) * abs(1  - self::$TIA) * $d['TI'] );
			$land = (self::$buildings[$b]['l'] );
			if ($spacesaving > 0.5 ) $spacesaving = $land;
			$land -= $spacesaving;


			if ($land > 0) {
				$tmp = round($d['L'] / $land);
				if ($tmp < $amount) $amount = $tmp;
			} 

		}

		if (self::$buildings[$b]['r'] > 0) {
			$tmp = round($d['R'] / self::$buildings[$b]['r']);
			if ($tmp < $amount) $amount = $tmp;
		}


		if (self::$buildings[$b]['i'] > 0) {
			$tmp = round($d['I'] / self::$buildings[$b]['i']);
			if ($tmp < $amount) $amount = $tmp;
		}

		if (self::$buildings[$b]['wo'] > 0) {
			$tmp = round($d['WO'] / self::$buildings[$b]['wo']);
			if ($tmp < $amount) $amount = $tmp;
		}

		if ($amount == 0) return "cannot build even one " . $this->translate($b) . "; " . $this->build($u, $b, 1);
		return $this->build($u, $b, $amount);
	}


	public function get_username_at_location($location) {

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

		$taken =$this->q("SELECT username FROM kingdom WHERE locations LIKE \"%," . $l . ",%\" OR locations LIKE \"" . $l . ",%\" OR locations LIKE \"%," . $l . "\"  OR locations = \"" . $l . "\";");
		if ($taken !== false) return $taken["username"];
		return false;

	}

	public function new_player($u) {
		$u = clean($u);

		$details = $this->q('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";');

		if ($details['locations'] == "") {

			$this->q('DELETE FROM kingdom WHERE username = "' . clean($u) . '";');
			//$this->q('DELETE FROM spells WHERE castby = "' . clean($u) . '" OR caston = "' . clean($u) . '";');
			$details = false;
		}
		if ($details === false) {


			$alreadynewplayer = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($u) . "\" AND caston = \"" . clean($u) . "\" AND spell = \"(newplayer)\" LIMIT 1;");
			if ($alreadynewplayer) {
				return "you've already created a new kingdom this turn. your people grow weary of being raped and murdered and cannot be compelled to form a new kingdom until next turn";
			} else {


				$this->q('DELETE FROM spells WHERE castby = "' . clean($u) . '" OR caston = "' . clean($u) . '";');
				$this->q('DELETE FROM items WHERE kingdom = "' . clean($u) );
				$this->q('DELETE FROM turnnotes WHERE fromuser = "' . clean($u) . '" OR touser = "' . clean($u) . '";');

				$loc = $this->random_location();
				while ($this->get_username_at_location($loc)) $loc = $this->random_location();

				$this->both($u, "creating new kingdom for " . $u . " at " . $loc . ". welcome to kingdoms!.");	

				$this->q(
						'INSERT INTO kingdom (username, locations, L, G, I, H, P, S,FA, BT, WO, R, D) VALUES ("' . clean($u) . '", "' . $loc . '", ' . self::$LPK . ', ' . self::$SG . ', ' . self::$SI . ', ' . self::$SH . ',' . self::$SP . ',' . self::$SS . ',' . self::$SFA . ',' . self::$SBT. ', ' . self::$SW . ', ' . self::$SR . ', ' . self::$SD . ');'
					);
				$details = $this->q('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";');
				$this->both($u, "to play, type commands here. check out the play guide for detailed help");

				$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"(newplayer)\", 1);");
				$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"protection\", 5);");
				$this->q("INSERT INTO items (kingdom, item, amountleft) VALUES (\"" . clean($u) . "\", \"time turner\",  20);");

			}


		}  else {
			$this->both($u, "welcome " . $u . "! your kingdom is at the following location(s): " . $details['locations'] );
			$this->both($u, "to play, you may either type commands in /kingdoms, or you can private message me. type !help for commands");
		}
	}

	public function autoannex($u) {
		$u = clean($u);

		$k = $this->get_kingdom($u);

		$locations = $this->make_loc_array($k['locations']);

		foreach($locations as $k => $l) {

			$loc = explode(":", $l);

			$x = $loc[0];
			$xmin = $loc[0] - 1; if ($xmin < 0) $xmin == 0;
			$xmax = $loc[0] + 1; if ($xmax > self::$SIZE) $xmax == self::$SIZE;

			$y = $loc[1];
			$ymin = $loc[1] - 1; if ($ymin < 0) $ymin == 0;
			$ymax = $loc[1] + 1; if ($ymax > self::$SIZE) $ymax == self::$SIZE;

			$msg = "the specified land is outside the bounds of the world";

			if ($this->get_username_at_location($xmin . ":" . $ymin) === false) $msg = $this->annex($u, $xmin . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmin . ":" . $ymax) === false) $msg = $this->annex($u, $xmin . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $ymin) === false) $msg = $this->annex($u, $xmax . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $ymax) === false) $msg = $this->annex($u, $xmax . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($x . ":" . $ymin) === false) $msg = $this->annex($u, $x . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmin . ":" . $y) === false) $msg = $this->annex($u, $xmin . ":" . $y);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $y) === false) $msg = $this->annex($u, $xmax . ":" . $y);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($x . ":" . $ymax) === false) $msg = $this->annex($u, $x . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;


		}

		return "no free space around your kingdom to annex!";
	} 

/*	public function autoannex_helper($d, $cloc) {

                $cloc =  explode(":", preg_replace('/[^0-9:]+/sm', '', $cloc));

                if (count($cloc) != 2) return "please use the command like this !annex number:number";

                $cloc[0] = intval($cloc[0]);
                $cloc[1] = intval($cloc[1]);

                //              $this->room($u . ' is trying to annex ' . $cloc[0] . ":" . $cloc[1]);

                if ($cloc[0] < 0 || $cloc[0] > self::$SIZE || $cloc[1] < 0 || $cloc[1] > self::$SIZE) return "the specified land is outside the bounds of the world, sorry.";


                $locations = $this->make_loc_array($d['locations']); //explode(",", $d['locations']);

                if ($d['P']  / (count($locations) + 1)  < self::$PPLS) {
                        $popneeded = (count($locations) + 1) * self::$PPLS - $d['P'];
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
                        $taken = $this->get_username_at_location($cloc[0] . ":" . $cloc[1]);
                        if ($taken !== false) return "that land is already owned by " . $taken;
                }

                if ($d['G'] < self::$CTA) return "you have insufficent gc to perform surveying of this site. the cost is " . self::$CTA . " gc";

                // got to here, so annex away

                $gold = $d['G'] - self::$CTA;
                $land = $d['L'] + self::$LPK;
                $locations = $d['locations'] . "," . $cloc[0] . ":" . $cloc[1];
                $this->q("UPDATE kingdom SET G=" . intval($gold) . ", L=" . intval($land) . ", locations=\"" . $locations . "\" WHERE username =\"" . clean($u) . "\" LIMIT 1;");

                return "land at " . $cloc[0] . ":" . $cloc[1] .  " annexed!";		
	}

	public function autoannex($u) {
		$u = clean($u);

		$k = $this->get_kingdom($u);

		$locations = $this->make_loc_array($k['locations']);

		foreach($locations as $k => $l) {

			$loc = explode(":", $l);

			$x = $loc[0];
			$xmin = $loc[0] - 1; if ($xmin < 0) $xmin == 0;
			$xmax = $loc[0] + 1; if ($xmax > self::$SIZE) $xmax == self::$SIZE;

			$y = $loc[1];
			$ymin = $loc[1] - 1; if ($ymin < 0) $ymin == 0;
			$ymax = $loc[1] + 1; if ($ymax > self::$SIZE) $ymax == self::$SIZE;

			$msg = "the specified land is outside the bounds of the world";

			if ($this->get_username_at_location($xmin . ":" . $ymin) === false) $msg = $this->autoannex_helper($k, $xmin . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmin . ":" . $ymax) === false) $msg = $this->autoannex_helper($k, $xmin . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $ymin) === false) $msg = $this->autoannex_helper($k, $xmax . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $ymax) === false) $msg = $this->autoannex_helper($k, $xmax . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($x . ":" . $ymin) === false) $msg = $this->autoannex_helper($k, $x . ":" . $ymin);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmin . ":" . $y) === false) $msg = $this->autoannex_helper($k, $xmin . ":" . $y);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($xmax . ":" . $y) === false) $msg = $this->autoannex_helper($k, $xmax . ":" . $y);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

			if ($this->get_username_at_location($x . ":" . $ymax) === false) $msg = $this->autoannex_helper($k, $x . ":" . $ymax);
			if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;


		}

		return "no free space around your kingdom to annex!";
	}

*/
	public function find_nearest_tile_of_user($u, $loca) {
		$u = clean($u);

		$k = $this->get_kingdom($u);

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
		$cloc =  explode(":", preg_replace('/[^0-9\:]+/sm', '', $cloc));
		$u = clean($u);
		$d = $this->get_kingdom($u);

		if (count($cloc) != 2) return "please use the command like this !annex number:number";

		$cloc[0] = intval($cloc[0]);
		$cloc[1] = intval($cloc[1]);

		if ($cloc[0] < 0 || $cloc[0] > self::$SIZE || $cloc[1] < 0 || $cloc[1] > 100) return "the specified land is outside the bounds of the world, sorry.";


		$playerlocs = $this->make_loc_array($d['locations']);

		if (count($playerlocs) == 1) return "you cannot obliterate your last remaining land! try !selfdestruct instead?";

		$playerlocsflip = array_flip($playerlocs);		

		$lands = count($playerlocs);

		if (isset($playerlocsflip[$cloc[0] . ':' . $cloc[1]]) == false) return "you cannot obliterate " . $cloc[0] . ":" . $cloc[1] . " as it does not belong to you.";

		unset($playerlocsflip[$cloc[0] . ':' . $cloc[1]]);

		$playerlocs = array_keys($playerlocsflip);

		$d['locations'] = implode(",", $playerlocs);

		$report = array();

		foreach ($d as $k => $v) {
			if ($k != 'username' && $k != 'locations' && $k != 'G') {
				$newitem = round( ($v * ($lands - 1)) / $lands );
				if ($v - $newitem > 0) {
					$report[] = ($v - $newitem) . " " . $this->translate($k);
				}
				$d[$k] = $newitem;
			}
		}


		$this->save_kingdom($d);

		return "your army sets fires across your land completely obliterating " . $cloc[0] . ":" . $cloc[1] . " and " . implode(", ", $report);

	}

	public function annex($u, $cloc) {
		$cloc =  explode(":", preg_replace('/[^0-9:]+/sm', '', $cloc));
		$u = clean($u);
		$d = $this->get_kingdom($u);

		if (count($cloc) != 2) return "please use the command like this !annex number:number";

		$cloc[0] = intval($cloc[0]);
		$cloc[1] = intval($cloc[1]);

		//		$this->room($u . ' is trying to annex ' . $cloc[0] . ":" . $cloc[1]);

		if ($cloc[0] < 0 || $cloc[0] > self::$SIZE || $cloc[1] < 0 || $cloc[1] > self::$SIZE) return "the specified land is outside the bounds of the world, sorry.";


		$locations = $this->make_loc_array($d['locations']); //explode(",", $d['locations']);

		if ($d['P']  / (count($locations) + 1)  < self::$PPLS) {
			$popneeded = (count($locations) + 1) * self::$PPLS - $d['P'];
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
			$taken = $this->get_username_at_location($cloc[0] . ":" . $cloc[1]);
			if ($taken !== false) return "that land is already owned by " . $taken;
		}

		if ($d['G'] < self::$CTA) return "you have insufficent gc to perform surveying of this site. the cost is " . self::$CTA . " gc";

		// got to here, so annex away

		$gold = $d['G'] - self::$CTA;
		$land = $d['L'] + self::$LPK;
		$locations = $d['locations'] . "," . $cloc[0] . ":" . $cloc[1];
		$this->q("UPDATE kingdom SET G=" . intval($gold) . ", L=" . intval($land) . ", locations=\"" . $locations . "\" WHERE username =\"" . clean($u) . "\" LIMIT 1;");

		return "land at " . $cloc[0] . ":" . $cloc[1] .  " annexed!";
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
		$this->q($updateq);

	}
	public function turn_kingdom($k) {


		$this->q('DELETE FROM items WHERE amountleft <= 0 AND kingdom = "' . clean($k['username']) . '";');

		$report = "your kingdom has been updated! here's what happened in the last round:\n";

		$activespells = $this->get_active_spells($k['username']);


		if (!isset($activespells['drought'])) $activespells['drought'] = 0;
		if (!isset($activespells['rain'])) $activespells['rain'] = 0;
		if (!isset($activespells['plague'])) $activespells['plague'] = 0;
		if (!isset($activespells['fire'])) $activespells['fire'] = 0;
		if (!isset($activespells['health'])) $activespells['health'] = 0;


		$k['G'] = round($k['G']);

		$t = round(self::$GPR + self::$GPB * $k['B'] + self::$IPF * $k['FR'] + ($k['P']*15)/($k['S'] + 1));
		$k['G'] += $t;
		$report .=  $t . " gc were added to your coffers! you now have " . ($k['G']) . " gc\n";




		$t = self::$FPF * $k['FA'] + self::$FPR * $k['FR'];

		$drought = rand(0, 30) * ($activespells['drought'] - $activespells['rain']);	
		if ($drought > 100) $drought = 100;
		$t *= ((100 - $drought)/100);
		$t = round($t);

		$k['F'] += $t;
		$report .= $t . " bushels were added to your food stocks." . ($drought > 0 ? " there is a drought over the land": ($drought < 0 ? " there are rains over the land": "")) . "\n";



		$t = round(self::$WPD * $k['D'] * (rand(60,140)/100) * ((100 - $drought)/100));

		if ($t < 0) $t = 0;

		$needdams = false;
		if ($t + $k['WA'] > self::$WDL * $k['D']) {
			$t = self::$WDL * $k['D'] - $k['WA'];
			$needdams = true;
		}

		$report .= $t . " liters of water flowed into to your dams\n";
		if ($needdams) $report .= "your dams are full. to continue collecting water you should build more";
		$k['WA'] += $t;


		$t = self::$FPP * ($k['P'] + $k['S']);
		$y = self::$WPP * ($k['P'] + $k['S']);

		$foodneeded = 0;
		$waterneeded = 0;
		if ($t > $k['F']) {
			$foodneeded = $t - $k['F'];
			$t = $k['F'];
		}

		if ($y > $k['WA']) {
			$waterneeded = $y - $k['WA'];
			$y = $k['WA'];
		}



		$k['F'] -= $t;
		$k['WA'] -= $y;

		$report .= $t . " bushels of food and " . $y . " liters of water were consumed by your population. your food stock is now " . $k['F'] .  " bushels and your water stock is now " . $k['WA'] . "\n";

		if ($foodneeded > 0) {
			$report .= $foodneeded . " more bushels were needed this round to sustain your population. make more farms! ";
			$starvedcivs = round($k['P'] * (rand(0, 10)/100));
			$starvedsold = round($k['S'] * (rand(0, 20)/100));
			if ($starvedcivs <= 0) $starvedcivs = 1;
			if ($starvedsold <= 0) $starvedsold = 1;
			$report .= "as a result of starvation " . $starvedcivs . " civilians and " . $starvedsold . " soldiers died!\n";
			$k['S'] -= $starvedsold;
			$k['P'] -= $starvedcivs;
			if ($k['S'] < 0) $k['S'] = 0;
			if ($k['P'] < 0) $k['P'] = 0;
		}

		if ($waterneeded > 0) {
			$report .= $waterneeded . " more liters were needed this round to sustain your population. make more dams! ";
			$starvedcivs = round($k['P'] * (rand(0, 10)/100));
			$starvedsold = round($k['S'] * (rand(0, 20)/100));
			if ($starvedcivs <= 0) $starvedcivs = 1;
			if ($starvedsold <= 0) $starvedsold = 1;
			$report .= "as a result of dehydration " . $starvedcivs . " civilians and " . $starvedsold . " soldiers died!\n";
			$k['S'] -= $starvedsold;
			$k['P'] -= $starvedcivs;
			if ($k['S'] < 0) $k['S'] = 0;
			if ($k['P'] < 0) $k['P'] = 0;
		}


		$plague = $activespells['plague'] - $activespells['health'];
		$civkilledbyplague = abs(rand(0, ($k['P'] > 20 ? $k['P']/5  : 3 )) * ($plague > 0));
		$soldkilledbyplague = abs(rand(0, ($k['S'] > 20 ? $k['S']/5 : 3 )) * ($plague > 0));


		$popcap = round(self::$PPH * $k['H']);

		$t = round(self::$PRR * $k['P']);
		if ($t + $k['P'] > $popcap) {
			$t = $popcap - $k['P'];
			if ($t < 0) $t = 0;
		}

		if ($foodneeded) $t = 0 ;

		$t -= $civkilledbyplague;	

		if ($plague > 0) $report .= "there is a plague over your kingdom. you may wish to !cast health on yourself\n";

		$k['P'] += $t;
		if ($t >= 0) {
			$report .= $t . " new people were added to your civilian population, you now have " . $k['P'] . " people\n";
		} else {
			$report .= abs($t) . " people were lost from your civilian population, you now have " . $k['P'] . " people\n";
		}



		$soldcap = round( self::$SPB * $k['BK'] );

		$t = round(self::$SRR * $k['BK']);
		if ($t + $k['S'] > $soldcap) {
			$t = $soldcap - $k['S'];
			if ($t < 0) $t = 0;
		}

		if ($foodneeded) $t = 0 ;
		$t -= $soldkilledbyplague;

		$k['S'] += $t;
		if ($t >= 0) {
			$report .= $t . " new soldiers were added to your military, you now have " . $k['S'] . " soldiers\n";
		} else {
			$report .= $t . " soldiers died from plague, you now have " . $k['S'] . " soldiers\n";
		}



		$horsecap = round(self::$HPS * $k['ST']);
		$t = round(self::$HRR * ($k['HO'] + 1));
		if ($t + $k['HO'] > $horsecap) {
			$t = $horsecap - $k['HO'];
			if ($t < 0) $t = 0;
		}

		if ($foodneeded) $t = 0 ;

		$k['HO'] += $t;
		$report .= $t . " new horses were added to your stables, you now have " . $k['HO'] . " horses\n";


		if ($k['P'] >= $popcap){
			$report .= "your population is unable to expand further because you only have " . $k['H'] . " houses\n";
		} 

		if ($k['S'] >= $soldcap){
			$report .= "your military is unable to expand further because you only have " . $k['BK'] . " barracks\n";
		}

		if ($k['HO'] >= $horsecap) {
			$report .= "your horses are unable to breed further because you only have " . $k['ST'] . " stables\n";

		}	

		if ($foodneeded) $report .= "your population is unable to expand because it is starving to death!\n";


		$t = round(self::$WOF * $k['FR'] * (1 + ($k['FR'] / 20 > 1 ? 1 : $k['FR'] / 20)));
		$report .= $t . " faggots of lumber were harvested\n";
		$k['WO'] += $t;


		/*
		   public static $WPD = 3; // water per dam
		   public static $WDL = 1000; // water dam limit
		   public static $SPQ = 2; // stone per quary
		   public static $HPS = 5; // 5 horses to a stable
		   public static $TIA = 0.95; // new foodprint of entire kingdom per technical institute

		 */

		$t = round(self::$SPQ * $k['Q'] * (1 + ($k['Q'] / 20 > 1 ? 1 : $k['Q'] / 20)));
		$report .= $t . " ton of stone was extracted\n";
		$k['R'] += $t;



		$t = round(self::$IPM * $k['MN'] * (1 + ($k['SM'] / 20 > 1 ? 1 : $k['SM'] / 20)));
		$report .= $t . " bars of iron were mined\n";
		$k['I'] += $t;

		$t = self::$WPF * $k['WF'] * (1 + ($k['SM'] / 40 > 1 ? 1 : $k['SM'] / 40));
		if ($t > $k['I']) $t = $k['I'];
		$t = round($t);
		$report .= $t . " weapons were produced. your iron stocks are now " . $k['I'] . "\n";
		$k['W'] += $t;
		$k['I'] -= $t;

		$t= self::$MPP * $k['PR'];
		$report .= $t . " magical runes were produced\n";
		$k['M'] += $t;


		$fire = $activespells['fire'] - $activespells['rain'];
		if ($fire > 0) {
			$k['B'] = round($fire * (rand(95, 100)/100) * $k['B']);
			$k['FA'] = round($fire * (rand(95, 100)/100) * $k['FA']);
			$k['FR'] = round($fire * (rand(95, 100)/100) * $k['FR']);
			$k['SM'] = round($fire * (rand(95, 100)/100) * $k['SM']);
			$k['BT'] = round($fire * (rand(95, 100)/100) * $k['BT']);		
			$k['WF'] = round($fire * (rand(95, 100)/100) * $k['WF']);		
			$k['BK'] = round($fire * (rand(95, 100)/100) * $k['BK']);		
			$k['TC'] = round($fire * (rand(95, 100)/100) * $k['TC']);		
			$k['H'] = round($fire * (rand(95, 100)/100) * $k['H']);		
			$k['T'] = round($fire * (rand(95, 100)/100) * $k['T']);		
			$k['IA'] = round($fire * (rand(95, 100)/100) * $k['IA']);		
			$report .= "magical fires razed some buildings from your land. you may want to !cast rain on yourself\n";
		}

		$result = $this->db->query("SELECT fromuser, notes FROM turnnotes WHERE touser = \"" . clean($k['username']) . "\";");

		if ($result) { 	

			if ($result->num_rows > 0) {
				while($notes = $result->fetch_assoc()) {
					$report .= $notes['notes'] . "\n";
				}
			}
		}

		$result = $this->db->query("SELECT item, amountleft FROM items WHERE kingdom = \"" . clean($k['username']) . "\";");

		if ($result && $result->num_rows > 0) {
			$report .= "your kingdom has the following magical items in its possession:\n";
			while($notes = $result->fetch_assoc())  $report .=  $notes['item'] . ' with ' . $notes['amountleft'] . ' uses remaining'. "\n";
		}

		$reporttime = time();
		$this->q("INSERT INTO reports (user, report, timestamp) VALUES (UNHEX('" . bin2hex($k['username']) . "'), UNHEX('" . bin2hex($report) . "'), ".$reporttime.") ON DUPLICATE KEY UPDATE report = UNHEX('" . bin2hex($report) . "'), timestamp = ".$reporttime.";");
		$this->q("DELETE FROM turnnotes WHERE touser = \"" . clean($k['username']) . "\";");

		$this->save_kingdom($k);
	}


	// iterates the game forward
	public function turn() {


		$result = $this->db->query("SELECT * FROM kingdom;");

		if ($result->num_rows > 0) {
			while($kingdom = $result->fetch_assoc()) {
				$this->turn_kingdom($kingdom);		
			}
		}

		$this->db->query("UPDATE worldvars SET value = value + 1 WHERE name = 'turns';");
		$this->db->query("UPDATE worldvars SET value = " . time() . " WHERE name = 'lastturn';");

		$this->turn_remove_old_spells();
		broadcast("<TURNED>");
	}

	public function calculate_defence_rating($k) {
		//def = BT * S * (S/W)
		$horsessoldier = 1 + ($k['HO'] / ($k['S'] + 1));

		return $k['BT'] * ($horsessoldier * $k['S']) * ($k['S']/($k['W'] + 1));
	}

	public function calculate_attack_rating($k) {
		//atk = S*(1 + BK/U)*(S/W)

		//return  (($k['S'] + ($k['U']*10)/($k['BK'] + 1))/($k['W'] + 1));


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
	public function conquer($attacker, $defender, $loc) {

		$loc = clean($loc);
		$attacker = clean($attacker);
		$defender = clean($defender);

		$a = $this->get_kingdom($attacker);
		$d = $this->get_kingdom($defender);

		if ($a === false) return $a . " does not have a kingdom";
		if ($d === false) return $d . " does not have a kingdom";

		$deflocs = $this->make_loc_array($d['locations']);
		$atklocs = $this->make_loc_array($a['locations']);

		$n = count($deflocs);

		$x = array_search($loc, $deflocs);
		if ($x === false) return $loc . " does not belong to " . $d['username'];

		$atklocs[] = $deflocs[$x];
		unset($deflocs[$x]);

		$a['locations'] = implode(',', $atklocs);
		$d['locations'] = implode(',', $deflocs);

		// conquest part

		$reportitems = array();
		foreach ($a as $k => $v) {
			if ($k != 'username' && $k != 'locations' && $k != 'G'){
				$amount = round($d[$k]/$n);
				$a[$k] += $amount;
				$d[$k] -= $amount;
				$text = $this->translate($k);
				if (!$text) $text = $this->item_translate($k);
				if (!$text) continue;
				if ($amount <= 0) continue;
				$reportitems[] = $amount . " " . $text;
			}
		}

		$report = "the spoils of war were: " . implode(', ', $reportitems);

		$this->save_kingdom($a);
		$this->save_kingdom($d);	

		if (count($deflocs) == 0) {
			$report .=  ". ". $d['username'] . " was defeated!";
			$this->add_turn_note($a['username'], $d['username'], "you were conquered completely by " . $a['username']);
		} else {
			$this->add_turn_note($a['username'], $d['username'], "some of your lands were conquered by " . $a['username']);
		}

		return $report;

	}

	public function attack($loc, $attacker) {



		$loc = explode(":", preg_replace('/[^0-9\:]/m', '', $loc));
		if (count($loc) != 2) return "invalid attack location specified!";
		$loc[0] = intval($loc[0]);
		$loc[1] = intval($loc[1]);
		$player = $this->get_username_at_location($loc[0] . ":" . $loc[1]);
		if (!$player) return "there is no kingdom at that location!";


		// if we are that this point then we are battling the $player kingdom for
		// $loc[0]:$loc[1] territory


		$a = $this->get_kingdom($attacker);

		$d = $this->get_kingdom($player);


		$protectedplayer = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($player) . "\" AND caston = \"" . clean($player) . "\" AND spell = \"protection\" LIMIT 1;");

		if ($protectedplayer !== false) return "attack failed, " . $player . " is magically protected for " . $protectedplayer['duration'] . " more turns.";


		$protectedplayer = $this->q("SELECT * FROM spells WHERE caston = \"" . clean($attacker) . "\" AND spell = \"protection\" LIMIT 1;");
		if ($protectedplayer !== false) return "you cannot attack other kingdoms while under a shield";



		if ($a['S'] == 0) return "you have no soldiers to attack with!";
		if ($a['W'] == 0) return "you have no weapons to attack with!";





		$alreadyattacked = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($a['username']) . "\" AND caston = \"" . clean($d['username']) . "\" AND spell = \"(attacked)\" LIMIT 1;");
		if ($alreadyattacked) return "you've already attacked " . $d['username'] . " this turn. your men are resting and cannot be compelled to attack again until next turn";

		$wardancedplayer = $this->q("SELECT * FROM spells WHERE caston = \"" . clean($player) . "\" AND spell = \"wardance\";");



		$this->room($a['username'] . ' is attacking ' . $d['username'] . "!");

		$atk = $this->calculate_attack_rating($a);
		$def = $this->calculate_defence_rating($d);

		$damageratio = $atk/($def + 1);

		// attacking breaks battlements down and uses weapons
		// maximum weapon-use is limited by maximum attacking soldiers

		$ruinedbattlements = round($d['BT'] * $damageratio);

		if ($ruinedbattlements > $d['BT']) $ruinedbattlements = $d['BT'];

		$ruinedweapons = rand($ruinedbattlements / 2, $ruinedbattlements *2);
		if ($ruinedweapons > $a['W']) $ruinedweapons = $a['W'];



		$battlementsleft = round($d['BT'] - $ruinedbattlements);
		if ($battlementsleft < 0) $battlementsleft = 0;


		$siegetowerslost = round(rand($ruinedbattlements/4, $ruinedbattlements/2));

		if ($siegetowerslost < 0) $siegetowerslost = 0;

		if ($siegetowerslost > $a['SI']) $siegetowerslost = $a['SI'];




		$defeatratio =  abs(($battlementsleft / ($d['BT'] + 1)));
		$victoryratio = abs(1 -($battlementsleft / ($d['BT'] + 1)));

		$attackersremaining = abs(round($a['S'] * (($victoryratio * rand(95,100))/100)));
		$defendersremaining = abs(round($d['S'] * (($defeatratio * rand(85,100))/(100/self::$HTA))));

		$attackerslost = $a['S'] - $attackersremaining;
		$defenderslost = $d['S'] - $defendersremaining;
		if ($attackerslost < 0) $attackerslost = 0;
		if ($defenderslost < 0) $defenderslost = 0;


		if ($defenderslost > $a['S']) $defenderslost = $a['S'];

		//if ($defenderslost > $attackerslost) $defenderslost = $attackerslost;

		$attackersremaining = abs($a['S'] - $attackerslost);
		$defendersremaining = abs($d['S'] - $defenderslost);

		$attackerremaininghorses = round($a['HO'] * (($victoryratio * rand(10,20))/20));
		if ($attackerremaininghorses < 0) $attackerremaininghorses = 0;
		$defenderremaininghorses = round($d['HO'] * (($defeatratio * rand(10,20))/(100/self::$HTA)));
		if ($defenderremaininghorses < 0) $defenderremaininghorses = 0;


		$attackerslosthorses = abs($a['HO'] - $attackerremaininghorses);
		$defenderslosthorses = abs($d['HO'] - $defenderremaininghorses);
		if ($attackerslosthorses < 0) $attackerslosthorses = 0;
		if ($defenderslosthorses < 0) $defenderslosthorses = 0;

		if ($attackerslosthorses > $a['HO']) $attackerslosthorses = $a['HO'];

		$attackerremaininghorses = abs($a['HO'] - $attackerslosthorses );
		$defenderremaininghorses = abs($d['HO'] - $defenderslosthorses );



		$report = "our army of " . $a['S'] . " soldiers carrying " . ($a['W'] > $a['S'] ? $a['S'] : $a['W']) . " weapons on " . ($a['HO'] > $a['S'] ? $a['S'] : $a['HO']) . " horses and " . $a['SI'] . " siege towers arrived and began the attack against " . $d['username'] . ". ";
		if ($damageratio > 4) $report .= "our force was a good match for " . $d['username'] . ". ";
		if ($damageratio <= 4) $report .= "our force was outmatched by " . $d['username'] . ". ";
		$report .= "we broke " . $ruinedbattlements . " battlements in the enemy's kingdom. ";
		$report .= "this cost us " . ($attackerslost) . " soldiers, " . abs($a['HO'] - $attackerremaininghorses ) . " horses, " . $ruinedweapons . " weapons and " . $siegetowerslost . " siege towers. ";
		$report .= "the enemy lost " . ($defenderslost) . " soldiers and " . abs($d['HO'] - $defenderremaininghorses ) . " horses. ";
		$report .= "we have returned to our kingdom and " . ($attackersremaining > 0 ? "are ready for another battle." : "need a new army.");

		$this->add_turn_note($a['username'], $d['username'], $a['username'] . " attacked your kingdom, and killed " . ($d['S'] - $defendersremaining) . " of our soldiers, and destroyed " . $ruinedbattlements . " battlements" );
		$this->room( $a['username'] . " attacked " .  $d['username'] . ", and killed " . ($d['S'] - $defendersremaining) . " of their soldiers, and destroyed " . $ruinedbattlements . " battlements" );

		$a['SI'] -= abs($siegetowerslost);
		$a['S'] = abs($attackersremaining);
		$a['W'] -= abs($ruinedweapons);
		$a['HO'] = abs($attackerremaininghorses);
		$d['S'] = abs($defendersremaining);
		$d['BT'] -= abs($ruinedbattlements);
		$d['HO'] = abs($defenderremaininghorses);

		if ($a['S'] < 0) $a['S'] = 0;
		if ($a['W'] < 0) $a['S'] = 0;
		if ($a['HO'] < 0) $a['S'] = 0;


		if ($d['S'] < 0) $d['S'] = 0;
		if ($d['W'] < 0) $d['S'] = 0;
		if ($d['HO'] < 0) $d['S'] = 0;


		$this->save_kingdom($a);
		$this->save_kingdom($d);


		if ($defendersremaining == 0 && $battlementsleft == 0 && $attackersremaining > 0) {		




			$report .= "\nwe have successfully conquered " . $d['username'] . "'s land at " . $loc[0] . ":" . $loc[1] . " ";

			$report .= $this->conquer($a['username'], $d['username'], $loc[0] . ":" . $loc[1]);		
			if ($wardancedplayer) {
				$report .= "\nwardance was cast on " . $d['username'] . " when our armies arrived, as a result the following additional lands were conquered:\n";

				$conquernum = rand(5,10);

				for ($i = 0; $i < $conquernum; $i++) {
					$l = $this->find_nearest_tile_of_user($d['username'], $loc[0] . ":" . $loc[1]);
					if ($l === false) break;
					$report .= $this->conquer($a['username'], $d['username'], $l);					
				}
			}


		}

		$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($a['username']) . "\", \"" . clean($d['username']) . "\", \"(attacked)\", 1);");


		return $report;

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

		$u = clean($u);
		$b = ($bs == "buy");

		if ($amount < 0) {
			$b = !$b;
			$amount = abs($amount);
		}

		$amount = intval($amount);
		$item = clean($item);

		if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

		$k = $this->get_kingdom(clean($u));
		$prices = $this->calculate_prices($k, !$b);



		if (!isset($prices[$item]))  {
			if (!isset($prices[$item . 's']))  {
				return "invalid item specified: " . $item . ". you can choose from " . implode(", ", array_keys($prices));
			} else $item .= 's';
		}
		$basecost = $amount * $prices[$item];

		$purchasecost = round($basecost * 1.00);
		$salecost = round($basecost *0.9);

		if ($b &&  $purchasecost > $k['G']) return "you do not have enough money for this trade! ";

		$t = $this->item_translate($item);
		if ($t === false) return "invalid item specified";

		if (!$b && $amount > $k[$t]) return "you do not have enough " . $item . " to sell that much!";

		// if we are here then they can cover their order

		if ($b) {

			if ($k[$t] + $amount > 32767) return "buying this many "  . $this->translate($t) . " would exceed the unit cap of 32767.";
			$k[$t] += $amount;
			$k['G'] -= $purchasecost;
		} else {
			$k[$t] -= $amount;
			$k['G'] += $salecost;
		}

		$this->save_kingdom($k);
		return ($b ? "bought " : "sold ") . $amount . " " . $item . ". you have " . $k['G'] . " gc in your coffers";
	}


	public function buymax($u, $item) {

		$u = clean($u);
		$item = clean($item);

		if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

		$k = $this->get_kingdom(clean($u));
		$prices = $this->calculate_prices($k, !$b);


		if (!isset($prices[$item]))  {
			if (!isset($prices[$item . 's']))  {
				return "invalid item specified: " . $item . ". you can choose from " . implode(", ", array_keys($prices));
			} else $item .= 's';
		}

		if ($prices[$item] <= 0) return "cannot buymax, error 1334";
		$t = $this->item_translate($item);


		$amount = 32767 - $k[$t];

		$tmp = round($k['G'] / $prices[$item]);

		if ($tmp < $amount) $amount = $tmp;

		if ($amount == 0) return "cannot buy even one " . $item . "; " . $this->trade($u, "buy", 1, $item);

		return $this->trade($u, "buy", $amount, $item);
	}

	public function sellall($u, $item) {

		$u = clean($u);
		$item = clean($item);

		if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

		$k = $this->get_kingdom(clean($u));


		$t = $this->item_translate($item);


		$amount = $k[$t];

		if ($amount == 0) return "you have no " . $item . " to sell.";

		return $this->trade($u, "sell", $amount, $item);
	}



	public function add_turn_note($from, $to, $note) {

		$from = clean($from);
		$to = clean($to);
		$note = clean_note($note);
		$this->q("INSERT INTO turnnotes (fromuser, touser, notes) VALUES (\"" . clean($from) . "\", \"" . clean($to) . "\", \"" . clean_note($note) . "\") ON DUPLICATE KEY UPDATE notes = CONCAT(notes, \"\\n\", \"" . clean_note($note) . "\");");
	}

	public function resolve_location_from_input($loc) {

		if (strrpos($loc, ":") !== false) {
			// location
			$player = $this->get_username_at_location($loc);
			if (!$player) return false;
		} else {
			$player = $this->get_kingdom($loc);
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
		$result = $this->db->query("SELECT * FROM spells WHERE caston = \"" . clean($u) . "\";");

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
		$this->db->query("UPDATE spells SET duration = duration - 1;");
		$this->db->query("DELETE FROM spells WHERE duration <= 0;");
	}

	public function  cast($spell, $victim, $attacker) {

		$victim = clean($victim);
		$attacker = clean($attacker);
		if (!isset(self::$spells[$spell])) return "not a valid spell. try !spells for a list";
		$s = self::$spells[$spell];
		$v = $this->get_kingdom($victim);
		if ($v === false) return $v . " does not have a kingdom";
		$a = $this->get_kingdom($attacker);
		if ($a === false) return $a . " cannot cast a spell because he/she does not own a kingdom. try !play";


		if ($victim != $attacker) {
			$protectedplayer = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($victim) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"protection\" LIMIT 1;");
			if ($protectedplayer !== false) return "spell was unsuccessful, as " . $victim. " is magically protected for " . $protectedplayer['duration'] . " more turns.";

			$protectedplayer = $this->q("SELECT * FROM spells WHERE caston = \"" . clean($attacker) . "\" AND spell = \"protection\" LIMIT 1;");
			if ($protectedplayer !== false) return "you cannot cast spells on other kingdoms while under a shield";


		}




		if ($a['M'] < $s['r']) return "you do not have enough magical runes to cast this spell!";

		if ($spell == 'shield') $spell = 'protection';

		if ($spell == 'protection' && $victim != $attacker) return "you can only cast protection on yourself";


		$r = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($attacker) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"" . clean($spell) . "\" LIMIT 1;");
		if ($r !== false) return "you've already cast this spell on " . $victim . " for this turn, please wait until the spell runs out before re-casting it.";

		$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($attacker) . "\", \"" . clean($victim) . "\", \"" . clean($spell) . "\", " . intval($s['l']) . ");");


		$a['M'] -= $s['r'];
		$this->save_kingdom($a);
		$this->room($attacker . " cast " . $spell . " on " . $victim . ". it will be active for " . $s['l'] . " turns");
		$this->add_turn_note($attacker, $victim, $spell . " was cast on your kingdom by " . $attacker);
		return $spell . " was cast on " . $victim . " successfully";

	}

	public function espionage($loc, $u) {

		$loc = clean($loc);
		$u = clean($u);



		$victim = $this->get_username_at_location($loc);
		if ($victim === false) return "cannot spy on " . $loc . " as no kingdom exists there.";

		$v = $this->get_kingdom($victim);
		$a = $this->get_kingdom($u);

		if ($v['username'] == $a['username']) return "you cannot spy on yourself. you could always use !stats";

		if ($a['IA'] == 0) return "you cannot spy until you have at least one intelligence agency. try !build intelligence agency";

		$ratio =  ($v['IA'] + 1) / ($a['IA'] + 1);

		$report = "your spies entered " . $v['username'] . "'s kingdom by night. ";


		if ($ratio < 0.25) {
			$report .= "their job was ridiculously easy as the enemy had relatively few of its own spies. ";
			$report .= $this->print_kingdom($v);
		} else if ($ratio < 0.75) {
			$report .= "they were able only to obtain limited information due the enemy's own spies. ";
			$report .= $v['username'] . "'s kingdom had:\nunused land: " . $v['L'] . ", gc: " . $v['G'] . ", food stocks: " . $v['F'] . ", soldiers: " . $v['S'] . ", civilians: " . $v['P'] . ", iron: " . $v['I'] . ", magic runes: " . $v['M']  . ", this was all the information we could obtain with our current level of espionage ability";
			$this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
		} else if ($ratio < 1) {
			$report .= "they were outnumbered by the enemy's own spies and obtained very little information. ";
			$report .= $v['username'] . "'s kingdom had:\nsoldiers: " . $v['S'] . ", civilians: " . $v['P'] . ", iron: " . $v['I'] . ", magic runes: " . $v['M']  . ", this was all the information we could obtain with our current level of espionage ability";
			$this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
			// third best
		} else if ($ratio < 10) {
			$report .= "they were vastly outnumbered by the enemy's own spies and obtained very little information. ";
			$report .= $v['username'] . "'s kingdom had:\nsoldiers: " . $v['S'] . ", this was all the information we could obtain with our current level of espionage ability";
			$this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
			// minimal
		}



		return $report;

	}

	public function thieve($loc, $u) {

		$loc = clean($loc);
		$u = clean($u);

		$victim = $this->get_username_at_location($loc);
		if ($victim === false) return "cannot thieve from " . $loc . " as no kingdom exists there.";

		$v = $this->get_kingdom($victim);
		$a = $this->get_kingdom($u);

		if ($v['username'] == $a['username']) return "you cannot thieve from yourself!";

		if ($a['T'] == 0) return "you cannot thieve until you have at least one thieves den. try !build thieves den";

		$alreadythieved = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($a['username']) . "\" AND caston = \"" . clean($v['username']) . "\" AND spell = \"(thieved)\" LIMIT 1;");
		if ($alreadythieved) return "you've already thieved " . $v['username'] . " this turn. your thieves are enjoying their spoils and cannot be persuaded to thieve this kingdom again until next turn";

		$ratio =  ($v['T']*1.2 + 1) / ($a['T'] + 1);

		$report = "your thieves snuck into " . $v['username'] . "'s kingdom. ";

		$steal = 0;
		if ($ratio < 0.25) {
			$report .= "they stole much, as the enemy had relatively few of its own thieves to keep you out. ";

			$steal = rand(0, 30);
		} else if ($ratio < 0.75) {
			$report .= "their reach was limited due being detected by the enemy's own thieves. ";
			$this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
			$steal = rand(0, 10);
		} else if ($ratio < 1) {
			$report .= "they were outnumbered by the enemy's own thieves. ";
			$steal = rand(0, 5);
			// third best
		} else if ($ratio < 10) {
			$report .= "they were vastly outnumbered by the enemy's own thieves. ";
			$steal = rand(0, 1);
			// minimal
		}

		$steal = round(($steal/100) * $v['G']);
		$report .= "we stole: " . $steal . " gc from the enemy. this gold has been added to our coffers";
		$v['G'] -= $steal;
		if ($v['G'] < 0) $v['G'] = 0;
		$a['G'] += $steal;

		$this->save_kingdom($v);
		$this->save_kingdom($a);

		$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($a['username']) . "\", \"" . clean($v['username']) . "\", \"(thieved)\", 1);");

		return $report;

	}

	public function yolo($u) {
		$u = clean($u);

		// check enough food


		$d = $this->get_kingdom($u);

		$popcap = round(self::$PPH * $d['H']);		
		$wineneeded =  ($d['P'] *5 +1);
		$foodneeded =  ($d['P'] *4 +1);

		if ($d['F'] < $foodneeded) return "your people cannot have an orgy without a sufficient feast first. you need at least " . $foodneeded . " bushels of food.";

		if ($d['WA'] < $wineneeded)  return "your people cannot have an orgy without a sufficient drink first. you need at least " . $wineneeded . " liters of water.";


		$newpeople = round(rand($d['P']/1.5, $d['P'] * 1.5)  / 3);

		if ($newpeople + $d['P'] > $popcap) return "your people cannot breed because you lack housing.";





		//$alreadyyolo = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($u) . "\" AND caston = \"" . clean($u) . "\" AND spell = \"(yolo)\" LIMIT 1;");
		//if ($alreadynewplayer) {
		//	 return "you've yolo'ed once this turn. your people are orgyed out and cannot be compelled to breed further until next turn";
		//} else {

		$d['P'] += $newpeople;
		$d['F'] -= $foodneeded;
		$d['WA'] -= $wineneeded;

		$report = "your people throw a massive feast, consuming " . $wineneeded . " flagons of wine and " . $foodneeded . " servings of food. after eating they retire to the orgarium for dirty cuddles. " . $newpeople . " new people were subsequently added to your kingdom's population.";

		$this->save_kingdom($d);
		//	$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"(yolo)\", 1);");

		return $report;
		//}



	}
	public function process_command($cmd, $u, $j, $type, $uid) {

		$isadmin = ($uid == '108380352791187456');

		$isgamemaster = ($uid == '210655291224752128');

		$cmd = strtolower($cmd);
		$c = explode(' ', $cmd);

		$u = clean($u);

		$p = ($type == "chat");

		if ($cmd == 'play') {
			$this->reply($j,$p, $this->new_player($u));
		} else if ($cmd == 'help') {
			$this->reply($j,$p, "just some of the commands: !stats !players !build !attack !annex !cast !trade !prices !spells !espionage !thieve and others. see play guide for details: http://www.sythe.org/threads/le-kingdoms-gameplay-manual.1601391/");
		} else if ($cmd == 'cash' || $cmd == 'money') {
			$k = $this->get_kingdom($u);
			if ($k === false) return $this->reply($j,$p, "you don't own a kingdom. try !play");
			return $this->reply($j,$p, "you have " . $k['G'] . " gc in your coffers");

		} else if ($cmd == 'space' || $cmd == 'land') {
			$k = $this->get_kingdom($u);
			if ($k === false) return $this->reply($j,$p, "you don't own a kingdom. try !play");
			return $this->reply($j,$p, "you have " . $k['L'] . " acres of spare land in your kingdom");

		} else if ($cmd == 'players') {
			$result = $this->db->query("SELECT username FROM kingdom WHERE locations <> \"\";");

			$kingdoms = array();
			if ($result->num_rows > 0) {
				while($kingdom = $result->fetch_assoc()) {
					$kingdoms[] = $kingdom['username'] ;
				}
			}
			return $this->reply($j,$p, "currently active kingdoms: " . implode(", ", $kingdoms));

		} else if ($cmd == 'thieve' || $c[0] == 'thieve' || $c[0] == 'steal') {
			if (count($c) < 2) return $this->reply($j,$p, "you can use your thieves dens to !thieve nn:mm or !thieve username other kingdoms");
			$loc =  $this->resolve_location_from_input($c[1]);
			if ($loc === false) return $this->reply($j,$p, "cannot thieve from " . $c[1] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
			$this->reply($j,$p, $this->thieve($loc, $u));

		} else if ($cmd == 'espionage' || $c[0] == 'espionage' || $c[0] == 'esp' || $c[0] == 'spy') {
			if (count($c) < 2) return $this->reply($j,$p, "you can use your intelligence agencies to !espionage nn:mm or !espionage username other kingdoms");
			$loc =  $this->resolve_location_from_input($c[1]);
			if ($loc === false) return $this->reply($j,$p, "cannot spy on " . $c[1] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
			$this->reply($j,$p, $this->espionage($loc, $u));


		} else if ($cmd == 'spells' || $c[0] == 'spells') {
			$report = "spells:\n";
			foreach(self::$spells as $s => $a) {
				$report .= $s . " costs " . $a['r'] . " runes and lasts " . $a['l'] . " turns and " . $a['d'] . "\n";
			}
			return $this->reply($j,$p, $report);

		} else if ($cmd == 'items' || $c[0] == 'items' ) {

			$result = $this->db->query("SELECT item, amountleft FROM items WHERE kingdom = \"" . clean($u) . "\";");
			$report = '';

			if ($result && $result->num_rows > 0) {
				$report .= "your kingdom has the following magical items in its possession:\n";
				while($notes = $result->fetch_assoc())  $report .=  $notes['item'] . ' with ' . $notes['amountleft'] . ' uses remaining'. "\n";

			} else {
				$report .= "your kingdom possesses no magical items at this time.";

			}
			return $this->reply($j,$p, $report);
		} else if ($cmd == 'use' || $c[0] == 'use' ) {
			if (count($c) < 2) return $this->reply($j,$p, "use [item]. e.g. use time turner");	
			array_shift($c);
			$item = clean_note(implode(' ', $c));

			$hasitem = $this->q('SELECT item, amountleft FROM items WHERE kingdom = "' . clean($u) . '" AND item = "'. clean_note($item) . '";');
			if (!$hasitem || $hasitem['amountleft'] <= 0) return $this->reply($j,$p, "you don't have any " . $item . " to use!");

			// has item

			$this->q('UPDATE items SET amountleft = amountleft - 1 WHERE kingdom = "' . clean($u) . '" AND item = "'. clean_note($item) . '";');

			if ($item == 'time turner') {
				$result = $this->db->query('SELECT * FROM kingdom WHERE username = "' . clean($u) . '";');

				if ($result->num_rows > 0 && $kingdom = $result->fetch_assoc()) {

					$this->turn_kingdom($kingdom);

					$this->reply($j,$p, "a magical dome encases the kingdom, accelerating local time. when the dome recedes you notice that a turn has passed. a report is prepared by your squire...");
					return $this->process_command("report",  $u, $j, $type);

				}

				return $this->reply($j,$p, "you don't have a kingdom!");


			} else {
				return $this->reply($j,$p, "using the item does nothing.");

			}


		} else if ($cmd == 'cast' || $c[0] == 'cast') {
			if (count($c) < 3) return $this->reply($j,$p, "you can cast a spell like so: !cast fire nn:mm, or !cast fire username. for a list of spells try !spells");
			$loc =  $this->resolve_location_from_input($c[2]);
			if ($loc === false) return $this->reply($j,$p, "cannot cast a spell on " . $c[2] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
			$c[1] = clean($c[1]);
			$c[2] = clean($c[2]);
			if (!isset(self::$spells[$c[1]])) return $this->reply($j,$p, "spell " . $c[1] . " is not a valid spell. try !spells for a list");
			$this->reply($j,$p, $this->cast($c[1], $c[2], $u));

		} else if ($cmd == 'prices') {
			$k = $this->get_kingdom(clean($u));
			$buyprices = $this->calculate_prices($k, false);
			$sellprices = $this->calculate_prices($k, true);

			$report = "prices:\n";
			foreach($buyprices as $i => $v) {
				$report .= $i . ": " . $v . ($sellprices[$i] == $v ? " gc\n" : " (sell: " . $sellprices[$i] . ") gc\n");
			}
			return $this->reply($j,$p, $report);

		} else if ($cmd == 'trade' || $c[0] == 'trade' || $c[0] == 'buy' || $c[0] == 'sell') {

			if ($c[0] != 'trade') array_unshift($c, 'trade');
			$k = $this->get_kingdom($u);
			if ($k['TC'] < 1) return $this->reply($j,$p, "you cannot trade until you have at least one trading center");
			if (count($c) < 4) return $this->reply($j,$p, "specify an item to trade using like so: !trade sell 10 wood. you can buy and sell food, water, wood, soldiers and stone.");
			if (!($c[1] == 'buy' || $c[1] == 'sell')) return $this->reply($j,$p, "you can't " . $c[1] . " you can only buy or sell. try for example: !trade buy 2 food");

			if (intval($c[2]) . "" == $c[2]) {
				$this->reply($j,$p, $this->trade($u, $c[1], $c[2], $c[3]));
			} else {
				$this->reply($j,$p, $this->trade($u, $c[1], $c[3], $c[2]));

			}

		} else if ($cmd == 'buymax' || $c[0] == 'buymax') {

			$k = $this->get_kingdom($u);
			if ($k['TC'] < 1) return $this->reply($j,$p, "you cannot trade until you have at least one trading center");
			if (count($c) != 2) return $this->reply($j,$p, "try !buymax wood");

			return $this->reply($j,$p, $this->buymax($u, clean($c[1])));


		} else if ($cmd == 'sellall' || $c[0] == 'sellall') {

			$k = $this->get_kingdom($u);
			if ($k['TC'] < 1) return $this->reply($j,$p, "you cannot trade until you have at least one trading center");
			if (count($c) != 2) return $this->reply($j,$p, "try !sellall wood");

			return $this->reply($j,$p, $this->sellall($u, clean($c[1])));

		} else if ($c[0] == 'gift' || $c[0] == 'give' || $c[0] == 'g') {

			if (count($c) < 3) return $this->reply($j,$p,"try !gift yourfriend 500");
			$k = $this->get_kingdom($u);
			$f = $this->get_kingdom($c[1]);

			if($k === false) 	return $this->reply($j,$p,"cannot !gift if you don't have a kingdom. try !play");
			if($f === false) return $this->reply($j,$p,$c[1] . " does not have a kingdom");

			if ($k['username'] == $f['username']) return $this->reply($j,$p,"you cannot gift to yourself");
			$amount = abs(intval($c[2]));
			if ($k['G'] < $amount) return $this->reply($j,$p,"you do not have sufficient gc to gift this amount");
			$k['G'] -= $amount;
			$f['G'] += $amount;

			$this->save_kingdom($k);
			$this->save_kingdom($f);
			return $this->room($u . " gifted " . $amount . " gc to " . $c[1]);

		} else if ($cmd == 'stats' || $cmd == 'stat' || $cmd == 's') {
			$n = (count($c) > 1 ? clean($c[1]) : clean($u) ); 
			$d = $this->get_kingdom($n);
			if ($d === false && $n == $u) return $this->reply($j,$p, "you don't have a kingdom, what are you poor? maybe try !play");
			else if ($d === false) return  $this->reply($j,$p, $n . " doesn't have a kingdom. you should invite him to !play");

			$this->reply($j,$p, $this->print_kingdom($d));
		} else if ($cmd == 'attack' || $c[0] == 'attack') {
			if (count($c) == 1) return $this->reply($j,$p, "to attack another kingdom use !attack nn:mm or !attack username");
			$loc = $c[1];
			if (strrpos($loc, ":") !== false) {
				// location
				$player = $this->get_username_at_location($loc);
				if (!$player) return $this->reply($j,$p, $loc . " is empty land. cannot attack it. maybe you could !annex instead?");
			} else {
				$player = $this->get_kingdom($loc);
				if ($player === false) return $this->reply($j,$p, $loc . " does not have a kingdom!");
				if (strrpos($player['locations'], ",") !== false) {
					$loc = explode(",",$player['locations']);
					$loc = $loc[0];
				} else $loc = $player['locations'];
				if ($loc == "") return $this->reply($j,$p,"that kingdom is already in ruins, has no lands!"); 

			}

			$this->reply($j,$p, $this->attack($loc, $u));

		} else if ($cmd == 'build' || $c[0] == 'build' || $c[0] == 'b') {
			if (count($c) <= 1) {
				$this->reply($j,$p, "try !build [building type] [amount]. for a list of buildings try !buildings");
			} else {
				$amount = 1;
				$buildingtype = $c[1];

				if (count($c) > 2) {
					if (intval($c[(count($c) - 1)]) . "" == $c[(count($c) - 1)]) {
						$amount = intval($c[(count($c) - 1)]);
						unset($c[(count($c) - 1)]);

					}
					unset($c[0]);
					$buildingtype = implode(" ", $c);

				}
				if ($buildingtype == "battlement") $buildingtype = "battlements";
				if ($buildingtype == "trading center") $buildingtype = "trade center";
				if ($buildingtype == "thieve") $buildingtype = "thieves den";
				if ($buildingtype == "thieves") $buildingtype = "thieves den";
				if ($buildingtype == "theives den") $buildingtype = "thieves den";
				if ($buildingtype == "theives") $buildingtype = "thieves den";

				if ($buildingtype == "trade") $buildingtype = "trade center";

				if ($buildingtype == "houses") $buildingtype = "house";


				$building = (isset(self::$buildings_lookup[$buildingtype]) ? self::$buildings_lookup[$buildingtype] : false);


				$this->reply($j,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->build($u, $building, $amount)));
			}

		} else if ($cmd == 'buildmax' || $c[0] == 'buildmax' || $c[0] == 'bm') {
			if (count($c) <= 1) {
				$this->reply($j,$p, "try !buildmax [building type]. for a list of buildings try !buildings");
			} else {
				unset($c[0]);
				$buildingtype = implode(" ", $c);

				if ($buildingtype == "battlement") $buildingtype = "battlements";
				if ($buildingtype == "trading center") $buildingtype = "trade center";
				if ($buildingtype == "thieve") $buildingtype = "thieves den";
				if ($buildingtype == "thieves") $buildingtype = "thieves den";
				if ($buildingtype == "theives den") $buildingtype = "thieves den";
				if ($buildingtype == "theives") $buildingtype = "thieves den";
				if ($buildingtype == "trade") $buildingtype = "trade center";
				if ($buildingtype == "houses") $buildingtype = "house";

				$building = (isset(self::$buildings_lookup[$buildingtype]) ? self::$buildings_lookup[$buildingtype] : false);

				$this->reply($j,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->buildmax($u, $building)));
			}
		} else if ($cmd == 'raze' || $c[0] == 'raze') {
			if (count($c) <= 1) {
				$this->reply($j,$p, "you can !raze [building type] [amount] to free up some land. for a list of buildings try !buildings");
			} else {
				$amount = 1;
				$buildingtype = $c[1];

				if (count($c) > 2) {
					if (intval($c[(count($c) - 1)]) . "" == $c[(count($c) - 1)]) {
						$amount = intval($c[(count($c) - 1)]);
						unset($c[(count($c) - 1)]);

					}
					unset($c[0]);
					$buildingtype = implode(" ", $c);

				}
				if ($buildingtype == "battlement") $buildingtype = "battlements";
				if ($buildingtype == "trading center") $buildingtype = "trade center";



				$building = (isset(self::$buildings_lookup[$buildingtype]) ? self::$buildings_lookup[$buildingtype] : false);


				$this->reply($j,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->raze($u, $building, $amount)));
			}


		} else if ($cmd == 'buildings') {
			$report = "buildings:\n";

			foreach (self::$buildings_key as $k => $v) {
				$costs = array();
				if (self::$buildings[$k]['wo'] > 0) $costs[] = self::$buildings[$k]['wo'] . " wood";
				if (self::$buildings[$k]['r'] > 0) $costs[] = self::$buildings[$k]['r'] . " stone";
				if (self::$buildings[$k]['i'] > 0) $costs[] = self::$buildings[$k]['i'] . " iron";
				if (self::$buildings[$k]['g'] > 0) $costs[] = self::$buildings[$k]['g'] . " gc";	
				$report .= $v . " ( " .  implode(", ", $costs) . ' ) : ' . self::$buildings[$k]['d'] . ".\n";
			}
			$this->reply($j,$p,$report);

		} else if ($cmd == 'annex' || $c[0] == 'annex') {
			if (count($c) <= 1) {
				return $this->reply($j,$p, "you can annex some unused surrounding land by !annex nn:mm, where nn:mm is the location of the land. this costs " . self::$CTA . " gc for surveying.");
			} else {
				$location =  preg_replace('/[^0-9:]+/sm', '', $c[1]);
				return $this->reply($j, $p, $this->annex(clean($u), $location));
			}
		} else if ($cmd == 'obliterate' || $c[0] == 'obliterate') {
			if (count($c) <= 1) {
				return $this->reply($j,$p, "you can obliterate some of your lands like so !obliterate nn:mm");
			} else {
				$location =  preg_replace('/[^0-9:]+/sm', '', $c[1]);
				return $this->reply($j, $p, $this->obliterate(clean($u), $location));
			}


		} else if ($cmd == 'autoannex' || $c[0] == 'autoannex') {
			$times = 1;
			if (count($c) == 2) $times = abs(intval($c[1]));
			$messages = array();

			if ($times > 20) return $this->reply($j, $p, "you can only autoannex at most 20 squares of land at a time");

			for ($i = 0; $i < $times; $i++) {
				$latest = $this->autoannex(clean($u));
				$messages[] =  $latest;
				if (strrpos($latest, "you have insufficent") !== false) break;
			}

			return $this->reply($j, $p, implode("\n", $messages));


		} else if ($c[0] == 'yolo') {
			return $this->reply($j,$p, $this->yolo(clean($u) ));

		} else if ($cmd == 'map') {



		} else if ($c[0] == 'turn' && ($isadmin || $isgamemaster)) {
			return $this->turn();
		} else if ($c[0] == 'destroy' && $isadmin) {
			$this->q('DELETE FROM kingdom WHERE username = "' . clean($c[1]) . '" LIMIT 1;');
			$this->reply($j,$p, "kingdom of " . clean($c[1]) . " has been razed to the ground!");
		} else if ($c[0] == 'selfdestruct') {
			$this->q('DELETE FROM kingdom WHERE username = "' . clean($u) . '" LIMIT 1;');	
			$this->reply($j,$p, "kingdom of " . $u . " ceased to exist. to start over !play");
		} else if ($cmd == 'fakeplay' && $isadmin) {
			$this->new_player("fakeplayer" . rand(100,10000));
		} else if ($c[0] == 'nuke' && $isadmin) {
			$this->q("DELETE FROM kingdom;");
			$this->q("DELETE FROM turnnotes;");
			$this->q("DELETE FROM spells;");
			$this->q("DELETE FROM reports;");
			$this->q("DELETE FROM items;");
			$this->q("UPDATE worldvars SET value = 0 WHERE name = 'turns';");
			array_shift($c);
			$text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));

			$this->q("UPDATE worldvars SET value = UNHEX('" . bin2hex($text) . "') where name = 'ageof';");
			return $this->room("sythe nuked the entire world. we welcome the new age of " . $text);
		} else if ($c[0] == 'rape' && $isadmin) {
			if (count($c) != 2) return 	$this->reply($j,$p, "you mean !rape fendle?");

			$k = $this->get_kingdom(clean($c[1]));
			$lostsold = $k['S'];
			$lostbattlements = $k['BT'];
			$lostpop = round($k['P'] / 2);


			$k['S'] = 0;
			$k['BT'] = 0;
			$k['P'] -= $lostpop;
			if ($k['P'] < 5) $k['P'] = 5;

			$this->room("a mysterious raping force appears at " . $c[1] . "'s kingdom. " . $lostsold . " soldiers have their bottoms violated and subsequently hobble from the kingdom never to be seen again. as a result " . $lostbattlements . " battlements fell apart and will need to be replaced. many civilians were also unpleasantly greeted in their bedrooms resulting in " . $lostpop . " fleeing the castle.");

			$this->save_kingdom($k);

		} else if ($cmd == 'bernanke' && $isadmin) {
			$this->room("sythe obtains 1000 gc for doing nothing");
			$k = $this->get_kingdom("sythe");
			$k['G'] += 1000;
			$this->save_kingdom($k);
		} else if ($cmd == 'greenspan' && $isadmin) {
			$this->room("sythe obtains 10000 gc for doing nothing");
			$k = $this->get_kingdom("sythe");
			$k['G'] += 10000;
			$this->save_kingdom($k);
		} else if ($cmd == 'obama' && $isadmin) {
			$this->room( "sythe set up an obama-matic printing press and fed the world's remaining common sense and decency into it, producing 1000000 gc");

			$k = $this->get_kingdom("sythe");
			$k['G'] += 1000000;
			$this->save_kingdom($k);
		} else if($c[0] == 'godeye' && $isadmin) {
			if (count($c) != 2) return $this->reply($j,$p, "you mean !godeye username");
			$k = $this->get_kingdom(clean($c[1]));
			if (!$k) return $this->reply($j,$p,"user " . $c[1] . " does not have a kingdom");

			return $this->reply($j,$p,$this->print_kingdom(  $k  ));

		} else if($c[0] == 'protect' && $isadmin) {

			if (count($c) != 3) return $this->reply($j,$p, "you mean !protect username turns");
			$k = $this->get_kingdom(clean($c[1]));
			if (!$k) return $this->reply($j,$p,"user " . $c[1] . " does not have a kingdom");

			$turns = intval($c[2]);
			if ($turns <= 0) $turns = 1;
			$this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($c[1]) . "\", \"" . clean($c[1]) . "\", \"protection\", " . intval($turns) . ") ON DUPLICATE KEY UPDATE duration = " . intval($turns) . ";");

			return $this->room( "sythe cast protection on " . $c[1] . " for " . intval($turns) . " turns.");

		} else if($c[0] == 'unprotect' && $isadmin) {

			if (count($c) != 2) return $this->reply($j,$p, "you mean !unprotect username");
			$k = $this->get_kingdom(clean($c[1]));
			if (!$k) return $this->reply($j,$p,"user " . $c[1] . " does not have a kingdom");

			$this->q("DELETE FROM spells WHERE caston = \"" . clean($c[1]) . "\", AND spell = \"protection\" LIMIT 1;");

			return $this->room( "sythe removed protection from " . $c[1]);


		} else if($c[0] == 'abra' && $isadmin) {

			if (count($c) != 5) return $this->reply($j,$p, "you mean !abra soldiers weapons horses battlements");
			$k = $this->get_kingdom(clean($u));

			$k['S'] = intval($c[1]);
			$k['W'] = intval($c[2]);
			$k['HO'] = intval($c[3]);
			$k['B'] = intval($c[4]);

			$this->save_kingdom($k);

			return $this->room( "sythe magicked himself an army.");

		} else if($c[0] == 'doubletime' && $isadmin) {		
			if (count($c) != 2) return $this->reply($j,$p, "you mean !doubletime username");

			$alreadyattacked = $this->q("DELETE FROM spells WHERE castby = \"sythe\" AND caston = \"" . clean($c[1]) . "\" AND spell = \"(attacked)\" LIMIT 1;");
			return $this->reply($j,$p, "sythe doubletimed " . $c[1] . ".");

		} else if ($cmd == 'dice' || $c[0] == 'dice' || $c[0] == 'roll' || $c[0] == 'r') {
			if (count($c) > 2) return $this->reply($j,$p, "you mean !dice [face count]");
			$faces = 6;
			if (count($c) == 2) $faces = abs(intval($c[1]));

			if ($faces <= 1) $faces = 6;
			return $this->reply($j,$p, "dice roll: " . (rand(0, $faces - 1) + 1) . " of " . $faces);

		} else if ($cmd == 'dicetext' || $c[0] == 'dt' || $c[0] == 'rt') {
			if (count($c) < 2) return $this->reply($j,$p, "you mean !dicetext option1,option2,option3");

			$faces = count($c);

			unset($c[0]);

			$text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));
			$options = explode(",",$text);

			$faces = count($options);

			return $this->reply($j,$p, "-> " . $options[(rand(0, $faces -1))]);


		} else if ($c[0] == 't') {
			array_shift($c);
			$text = preg_replace('/[^a-zA-Z0-9\?: .\-,]/m', '', implode(" ", $c));
			broadcast($u . ': ' . $text );
		} else if ($c[0] == 'checkturn' || $cmd == 'checkturn') {
			$r = $this->q("SELECT value FROM worldvars WHERE name='turns';");
			$turn = $r['value'];
			$r = $this->q("SELECT value FROM worldvars WHERE name='lastturn';");
			$lastturntime = intval($r['value']);
			$r = $this->q("SELECT value FROM worldvars WHERE name='turnfreq';");
			$turnfreq = intval($r['value']);
			$this->reply($j,$p, "<C>" . ($turnfreq - (time() - $lastturntime)));
			if ($this->lastturn <> $turn) {
				$this->reply($j,$p, "the world has turned! here's your report:");
				return $this->process_command("report",  $u, $j, $type);
			}
		} else if ($c[0] == 'report' || $cmd == 'report') {

			$r = $this->q("SELECT value FROM worldvars WHERE name='ageof';");
			$ageof = $r['value'];
			$r = $this->q("SELECT value FROM worldvars WHERE name='turns';");
			$turn = $r['value'];
			$this->lastturn = $turn;
			$report = $this->q("SELECT report, timestamp FROM reports WHERE user = UNHEX('" . bin2hex($u) . "');");
			//			if (!$report)  $this->reply($j,$p, "no reports yet. wait for the world to turn");
			$timepassed = time() - intval($report['timestamp']);
			$timepassed =  $timepassed / 3600;
			return $this->reply($j,$p, "You are playing Kingdoms, the age of " . $ageof . ", the world is " . $turn . " turns old. Your last report was generated " . (  intval($report['timestamp']) == 0 ? '... never as you are yet to play through a turn.' : round($timepassed * 10)/10 . " hours ago:\n" . $report['report']));
		}

	}

}



KingdomsGame::$buildings_key = array(
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

KingdomsGame::$buildings = array(
		"B" => 	array("wo" => 10, "r" => 0, "i"=> 10,  "l" => 2,	"g" => 100,	"d" => "generates income"),
		"FA" => 	array("wo" => 10, "r" => 0, "i" => 0, "l" => 10,	"g" => 50,	"d" => "generates food"),
		"MN" => 	array("wo" => 5, "r" => 0, "i" => 0, "l" => 5,	"g" => 100,	"d" => "generates iron"),
		"FR" => 	array("wo" => 0, "r" => 0, "i" => 0, "l" => 10,	"g" => 30,	"d" => "generates wood, and some food"),
		"SM" => 	array("wo" => 10, "r" => 10, "i" => 0, "l" => 2,	"g" => 20,	"d" => "boosts mining, weapons factories"),
		"BT" => 	array("wo" => 20, "r" => 20, "i" => 10, "l" => 2,	 "g" => 25,	"d" => "increases the kingdom's defences"),
		"U" => 	array("wo" => 20, "r" => 0, "i" => 0, "l" => 10,	 "g" => 50,	"d" => "increases your army's prowess"),
		"PR" => 	array("wo" => 20, "r" => 0, "i" => 0, "l" => 5,	 "g" => 60,	"d" => "generates magical runes"),
		"WF" =>	array("wo" => 10, "r" => 0, "i" => 0, "l" => 2,	 "g" => 50,	"d" => "generates weapons from iron"),
		"BK" => 	array("wo" => 10, "r" => 0, "i" => 0, "l" => 5,	 "g" => 100,	"d" => "houses and trains soldiers"),
		"TC" =>	array("wo" => 40, "r" => 0, "i" => 10, "l" => 20, 	"g" => 50,	"d" => "allows you to !trade commodities"),
		"H" =>		array("wo" => 5, "r" => 0, "i" => 0, "l" => 5, 	"g" => 40,	"d" => "generates population"),
		"T" =>	array("wo" => 5, "r" => 0, "i" => 0, "l" => 1, 	"g" => 20,	"d" => "allows you to thieve from other kingdoms"),
		"IA" => array("wo" => 20, "r" => 5, "i" => 0, "l" => 2, "g" => 100, "d" => "gathers information on other kingdoms"),
		"D" => array("wo" => 5, "r" => 20, "i" => 0, "l" => 30, "g" => 100, "d" => "creates water for your population"),
		"ST" => array("wo" => 15, "r" => 5, "i" => 0, "l" => 20, "g" => 75, "d" => "generates and stables horses"),
		"Q" => array("wo" => 50, "r" => 0, "i" => 0, "l" => 15, "g" => 40, "d" => "generates stone"),
		"TI" => array("wo" => 50, "r" => 5, "i" => 5, "l" => 10, "g" => 50, "d" => "increases space efficiency"),
		"SI" => array("wo" => 5000, "r" => 100, "i" => 1000, "l" => 5, "g" => 5000, "d" => "advanced attack unit, increases attack rating"),
		"WM" => array("wo" => 32767, "r" => 32767, "i" => 32767, "l" => 1000, "g" => 50000000, "d" => "war machine... for obliterating opponents")
);

KingdomsGame::$buildings_lookup = array_flip(KingdomsGame::$buildings_key);

KingdomsGame::$spells = array (
		"drought" => array("r" => 50, "l" => 1, "d" => "decreases the productivity of enemy farms"),
		"rain" => array("r" => 10, "l" => 1, "d" => "increases the productivity of farms and forests and decreases damage from fires"),
		"plague" => array("r" => 50, "l" => 1, "d" => "kills a random percentage of the enemy population"),
		"fire" => array("r" => 50, "l" => 1, "d" => "razes a random number of enemy buildings to the ground"),
		/*		"shield" => array("r" => 300, "l" => 1, "d" => "defends against attacks for one round"),*/
		"protection" => array("r" => 3000, "l" => 3, "d" => "defends against attacks for three rounds"),
		"health" => array("r" => 20, "l" => 2, "d" => "defends against plagues"),
		"wardance" => array("r" => 3000, "l" => 1, "d" => "attacks on the target can capture up to 10 squares of land")
		);


$discord = new Discord([
		'token' => '#### DISCORD BOT TOKEN HERE ####'
		]);



$discord->on('ready', function ($discord) {

		$discord->on('message', function ($message, $discord) {
			if (/*$message->channel_id == '210310674348376065' &&*/ strlen($message->content) > 0 && $message->content[0] == '!') {

			$guild = $discord->guilds->first();
			$glochannel = null;
			foreach ($guild->channels as $c) {
			//	echo "chan: " . $c->id;
					if ($c->id == '210310674348376065') {
						$glochannel = $c;
						break;
					}
			}

			if ($message->content == '!map') {
				$message->reply("Map is here: http://img.sythe.org/kingdomsmap.php although ShinBot will do a quick image for you if you ask !m nicely");
				return;
			}

			echo "\n" . "channel: " . print_r($channel, true);
			$bot = new KingdomsGame('#### DB HOST', '#### DB USERNAME ', '#### DB PASS', '#### DB SCHEMANAME', $message->channel, $glochannel);
			//		$message->reply('c');
			$username = "";

			$row = $bot->q("select username from discord_player where guid = UNHEX('" . bin2hex('' . $message->author->id) . "');") or print(mysql_error());
			if ($row === FALSE) {

				$bot->q("insert into discord_player (guid, username) values ( UNHEX('" . bin2hex(''.$message->author->id) . "'), UNHEX('" . bin2hex($message->author->username) . "'));") or print(mysql_error());
				$row = $bot->q("select username from discord_player where guid = UNHEX('" . bin2hex(''.$message->author->id) . "');")  or print(mysql_error());
			}

			if (!array_key_exists('username', $row)) {
				$message->reply("Not sure who you are <@" . $message->author->id . ">");
				return;	
			}

			$username = $row['username'];
			$bot->process_command(str_replace("\r", "", str_replace("\n", "", str_replace("!", "", $message->content))), $username,$username, "chat", ''.$message->author->id);	
			}
		});
});

$discord->run();


?>







