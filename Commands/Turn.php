<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:45
 */
class Turn extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $result = $this->__db->executeQuery("SELECT * FROM kingdom;")->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            foreach($result as $kingdom)
            {
                $this->turn_kingdom($kingdom);
            }
        }

        $this->__db->executeQuery("UPDATE worldvars SET value = value + 1 WHERE name = 'turns';");
        $this->__db->executeQuery("UPDATE worldvars SET value = " . time() . " WHERE name = 'lastturn';");

        KingdomHelper::turn_remove_old_spells();
        $this->__communicator->sendBoth($this->__message->getAuthorName(), "<TURNED>");
    }

    public function turn_kingdom($k) {

        $this->__db->executeQuery('DELETE FROM items WHERE amountleft <= 0 AND kingdom = "' . clean($k['username']) . '";');

        $report = "your kingdom has been updated! here's what happened in the last round:\n";

        $activespells = KingdomHelper::get_active_spells($k['username']);


        if (!isset($activespells['drought'])) $activespells['drought'] = 0;
        if (!isset($activespells['rain'])) $activespells['rain'] = 0;
        if (!isset($activespells['plague'])) $activespells['plague'] = 0;
        if (!isset($activespells['fire'])) $activespells['fire'] = 0;
        if (!isset($activespells['health'])) $activespells['health'] = 0;


        $k['G'] = round($k['G']);

        $t = round(ROUND_GOLD + ROUND_GOLD_PER_BANK * $k['B'] + INCOME_PER_FOREST * $k['FR'] + ($k['P']*15)/($k['S'] + 1));
        $k['G'] += $t;
        $report .=  $t . " gc were added to your coffers! you now have " . ($k['G']) . " gc\n";




        $t = FOOD_PER_FARM * $k['FA'] + FOOD_PER_FOREST * $k['FR'];

        $drought = rand(0, 30) * ($activespells['drought'] - $activespells['rain']);
        if ($drought > 100) $drought = 100;
        $t *= ((100 - $drought)/100);
        $t = round($t);

        $k['F'] += $t;
        $report .= $t . " bushels were added to your food stocks." . ($drought > 0 ? " there is a drought over the land": ($drought < 0 ? " there are rains over the land": "")) . "\n";



        $t = round(WATER_PER_DAM * $k['D'] * (rand(60,140)/100) * ((100 - $drought)/100));

        if ($t < 0) $t = 0;

        $needdams = false;
        if ($t + $k['WA'] > WATER_DAM_LIMIT * $k['D']) {
            $t = WATER_DAM_LIMIT * $k['D'] - $k['WA'];
            $needdams = true;
        }

        $report .= $t . " liters of water flowed into to your dams\n";
        if ($needdams) $report .= "your dams are full. to continue collecting water you should build more";
        $k['WA'] += $t;


        $t = FOOD_CONSUMED_PER_HEAD_POPULATION * ($k['P'] + $k['S']);
        $y = WATER_CONSUMED_PER_HEAD_POPULATION * ($k['P'] + $k['S']);

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


        $popcap = round(PEOPLE_PER_HOUSE * $k['H']);

        $t = round(POPULATIO_NREPRODUCTION_RATE * $k['P']);
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



        $soldcap = round( SOLDIERS_PER_BARRACKS * $k['BK'] );

        $t = round(ROUND_SOLDIERS_PER_BARRACKS * $k['BK']);
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



        $horsecap = round(HORSES_PER_STABLE * $k['ST']);
        $t = round(HORSE_REPRODUCTION_RATE * ($k['HO'] + 1));
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


        $t = round(WOOD_PER_FOREST * $k['FR'] * (1 + ($k['FR'] / 20 > 1 ? 1 : $k['FR'] / 20)));
        $report .= $t . " faggots of lumber were harvested\n";
        $k['WO'] += $t;


        /*
           public static $WPD = 3; // water per dam
           public static $WDL = 1000; // water dam limit
           public static $SPQ = 2; // stone per quary
           public static $HPS = 5; // 5 horses to a stable
           public static $TIA = 0.95; // new foodprint of entire kingdom per technical institute

         */

        $t = round(STONE_PER_QUARRY * $k['Q'] * (1 + ($k['Q'] / 20 > 1 ? 1 : $k['Q'] / 20)));
        $report .= $t . " ton of stone was extracted\n";
        $k['R'] += $t;



        $t = round(ROUND_IRON_PER_MINE * $k['MN'] * (1 + ($k['SM'] / 20 > 1 ? 1 : $k['SM'] / 20)));
        $report .= $t . " bars of iron were mined\n";
        $k['I'] += $t;

        $t = ROUND_WEAPON_PER_FACTORY * $k['WF'] * (1 + ($k['SM'] / 40 > 1 ? 1 : $k['SM'] / 40));
        if ($t > $k['I']) $t = $k['I'];
        $t = round($t);
        $report .= $t . " weapons were produced. your iron stocks are now " . $k['I'] . "\n";
        $k['W'] += $t;
        $k['I'] -= $t;

        $t= MAGIC_PER_PRIESTHOOD * $k['PR'];
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

        $result = $this->__db->executeQuery("SELECT fromuser, notes FROM turnnotes WHERE touser = \"" . clean($k['username']) . "\";")->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            if (count($result) > 0) {
                foreach($result as $notes)
                {
                    $report .= $notes['notes'] . "\n";
                }
            }
        }

        $result = $this->__db->executeQuery("SELECT item, amountleft FROM items WHERE kingdom = \"" . clean($k['username']) . "\";")->fetchAll(PDO::FETCH_ASSOC);

        if ($result && count($result) > 0) {
            $report .= "your kingdom has the following magical items in its possession:\n";
            foreach($result as $notes)
            {
                $report .=  $notes['item'] . ' with ' . $notes['amountleft'] . ' uses remaining'. "\n";
            }
        }

        $reporttime = time();
        $this->__db->executeQuery("INSERT INTO reports (user, report, timestamp) VALUES (UNHEX('" . bin2hex($k['username']) . "'), UNHEX('" . bin2hex($report) . "'), ".$reporttime.") ON DUPLICATE KEY UPDATE report = UNHEX('" . bin2hex($report) . "'), timestamp = ".$reporttime.";");
        $this->__db->executeQuery("DELETE FROM turnnotes WHERE touser = \"" . clean($k['username']) . "\";");

        $this->__db->saveKingdom($k);
    }
}