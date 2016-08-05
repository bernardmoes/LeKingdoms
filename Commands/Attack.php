<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Attack extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
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

    function attackNow()
    {
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

    function execute()
    {
        if (count($c) == 1) return $this->reply($user,$p, "to attack another kingdom use !attack nn:mm or !attack username");
        $loc = $c[1];
        if (strrpos($loc, ":") !== false) {
            // location
            $player = $this->get_username_at_location($loc);
            if (!$player) return $this->reply($user,$p, $loc . " is empty land. cannot attack it. maybe you could !annex instead?");
        } else {
            $player = $this->get_kingdom($loc);
            if ($player === false) return $this->reply($user,$p, $loc . " does not have a kingdom!");
            if (strrpos($player['locations'], ",") !== false) {
                $loc = explode(",",$player['locations']);
                $loc = $loc[0];
            } else $loc = $player['locations'];
            if ($loc == "") return $this->reply($user,$p,"that kingdom is already in ruins, has no lands!");

        }

        $this->reply($user,$p, $this->attack($loc, $user));
    }
}
