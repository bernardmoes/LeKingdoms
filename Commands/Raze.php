<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:42
 */
class Raze extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function raze($u, $b, $a)
    {
        $b = preg_replace('/[^A-Z]+/sm', '', $b);
        $a = abs(intval($a));

        $d = $this->__db->getKingdom(clean($u));
        if ($d === false) return "user does not exist";
        if (!isset(KingdomHelper::$buildings[$b])) {
            if (!isset(KingdomHelper::$buildings[$b . 's'])) {

                return "building type does not exist (" . $b . ")";
            } else $b .= 's';
        }
        if ($a <= 0) $a = 1;

        if ($b == "TI") return "cannot raze technical institutes";

        $spacesaving = ( (KingdomHelper::$buildings[$b]['l'] * $a) * abs(1  - TECHNICAL_INSTITUTE_ADVANTAGE_RATE) * $d['TI'] );
        $land = (KingdomHelper::$buildings[$b]['l'] * $a);
        if ($spacesaving > 0.5 * $land) $spacesaving = 0.5 * $land;
        $land -= $spacesaving;


        if ($d[$b] < $a) return "not enough of that type of building exist to be destroyed. only " . $d[$b] . " exist";

        $d['L'] += $land;



        $d[$b] -= $a;

        $this->__db->saveKingdom($d);

        return "your soldiers knock the foundations from under the " . KingdomHelper::translate($b) . " wiping out a total of " . $a . " " . KingdomHelper::translate($b) . " and freeing up " . $land . " acres of space";
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) <= 1) {
            $this->__communicator->sendReply($this->__message->getAuthorName(), "you can !raze [building type] [amount] to free up some land. for a list of buildings try !buildings");
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



            $building = (isset(KingdomHelper::$buildings_lookup[$buildingtype]) ? KingdomHelper::$buildings_lookup[$buildingtype] : false);


            $this->__communicator->sendReply($this->__message->getAuthorName(), ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->raze($this->__message->getAuthorName(), $building, $amount)));
        }


    }
}