<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:41
 */
class Build extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    public function buildNow($b, $a)
    {
        $u = $this->__message->getAuthorName();
        $b = preg_replace('/[^A-Z]+/sm', '', $b);
        $a = abs(intval($a));

        $d = $this->__kingdom;
        if ($d === false) return "user does not exist";
        if (!isset(KingdomHelper::$buildings[$b])) {
            if (!isset(KingdomHelper::$buildings[$b . 's'])) {

                return "building type does not exist (" . $b . ")";
            } else $b .= 's';
        }
        if ($a <= 0) $a = 1;

        $gold = (KingdomHelper::$buildings[$b]['g'] * $a);
        $spacesaving = ( (KingdomHelper::$buildings[$b]['l'] * $a) * abs(1  - TECHNICAL_INSTITUTE_ADVANTAGE_RATE) * $d['TI'] );

        $land = (KingdomHelper::$buildings[$b]['l'] * $a);

        if ($spacesaving > 0.5 * $land) $spacesaving = 0.5 * $land;
        $land -= $spacesaving;

        $rock = (KingdomHelper::$buildings[$b]['r'] * $a);
        $iron = (KingdomHelper::$buildings[$b]['i'] * $a);
        $wood = (KingdomHelper::$buildings[$b]['wo'] * $a);

        $report = "";

        if ($d['G'] < $gold) $report .= "not enough gold, needed at least " . $gold . " gc. ";
        if ($d['L'] < $land) $report .= "you do not have enough free space in your kingdom, needed at least " . $land . " acres. try !annex? ";
        if ($d['R'] < $rock) $report .= "not enough stone, needed at least " . $rock . " tons. ";
        if ($d['I'] < $iron) $report .= "not enough iron, needed at least " . $iron . " iron bars. ";
        if ($d['WO'] < $wood) $report .= "not enough wood, needed at least " . $wood . " faggots. ";

        if ($d[$b] + $a > 32767) $report .= "building this many " . KingdomHelper::translate($b) . " would exceed the maximum allowable structures of 32767. ";

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

        //$this->__db->executeQuery(("UPDATE kingdom SET WO=" . intval($woodleft) . ", R=" . intval($rockleft) . ", I=" . intval($ironleft) . ", G=" . intval($goldleft) . ", L=" . intval($landleft) . ", " . clean($b) . "=" . clean($built) . " WHERE username = \"" . clean($u) . "\" LIMIT 1;");

        $d['G'] = $goldleft;
        $d['L'] = $landleft;
        $d['R'] = $rockleft;
        $d['I'] = $ironleft;
        $d['WO'] = $woodleft;
        $d[$b] = $built;

        $this->__db->saveKingdom($d);
        return "built " . $a . " " . KingdomHelper::$buildings_key[$b] . ". you have " . $rep . " remaining";
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) <= 1) {
           $this->__communicator->sendReply($this->__message->getAuthorName(), "try !build [building type] [amount]. for a list of buildings try !buildings");
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


            $building = (isset(KingdomHelper::$buildings_lookup[$buildingtype]) ? KingdomHelper::$buildings_lookup[$buildingtype] : false);


           $this->__communicator->sendReply($this->__message->getAuthorName(), ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->buildNow($building, $amount)));
        }
    }
}