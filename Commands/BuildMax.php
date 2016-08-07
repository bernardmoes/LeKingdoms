<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 7-8-2016
 * Time: 20:02
 */
class BuildMax extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function buildMaxNow($b)
    {
        $u = $this->__message->getAuthorName();
        $b = preg_replace('/[^A-Z]+/sm', '', $b);

        $d = $this->__kingdom;
        if ($d === false) return "user does not exist";

        if (!isset(KingdomHelper::$buildings[$b])) {
            if (!isset(KingdomHelper::$buildings[$b . 's'])) {

                return "building type does not exist (" . $b . ")";
            } else $b .= 's';
        }



        $amount = 32767 - $d[$b];

        if (KingdomHelper::$buildings[$b]['g'] > 0) {
            $tmp = $d['G'] / KingdomHelper::$buildings[$b]['g'];
            if ($tmp < $amount) $amount = $tmp;
        }


        if ($d['L'] == 0) {
            $amount = 0;
        } else {
            $spacesaving = ( (KingdomHelper::$buildings[$b]['l'] ) * abs(1  - TECHNICAL_INSTITUTE_ADVANTAGE_RATE) * $d['TI'] );
            $land = (KingdomHelper::$buildings[$b]['l'] );
            if ($spacesaving > 0.5 ) $spacesaving = $land;
            $land -= $spacesaving;


            if ($land > 0) {
                $tmp = round($d['L'] / $land);
                if ($tmp < $amount) $amount = $tmp;
            }

        }

        if (KingdomHelper::$buildings[$b]['r'] > 0) {
            $tmp = round($d['R'] / KingdomHelper::$buildings[$b]['r']);
            if ($tmp < $amount) $amount = $tmp;
        }


        if (KingdomHelper::$buildings[$b]['i'] > 0) {
            $tmp = round($d['I'] / KingdomHelper::$buildings[$b]['i']);
            if ($tmp < $amount) $amount = $tmp;
        }

        if (KingdomHelper::$buildings[$b]['wo'] > 0) {
            $tmp = round($d['WO'] / KingdomHelper::$buildings[$b]['wo']);
            if ($tmp < $amount) $amount = $tmp;
        }

        if ($amount == 0) return "cannot build even one " . KingdomHelper::translate($b) . "; " . (new Build($this->__commandEvaluator))->buildNow($b, 1);
        return (new Build($this->__commandEvaluator))->buildNow($b, $amount);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) <= 1) {
            $this->__communicator->sendReply($this->__message->getAuthorName(), "try !buildmax [building type]. for a list of buildings try !buildings");
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

            $building = (isset(KingdomHelper::$buildings_lookup[$buildingtype]) ? KingdomHelper::$buildings_lookup[$buildingtype] : false);

            $this->__communicator->sendReply($this->__message->getAuthorName(), ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->buildMaxNow($building)));
        }
    }
}