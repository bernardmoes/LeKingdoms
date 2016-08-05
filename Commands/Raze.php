<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:42
 */
class Raze extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function raze()
    {
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

    function execute()
    {
        if (count($c) <= 1) {
            $this->reply($user,$p, "you can !raze [building type] [amount] to free up some land. for a list of buildings try !buildings");
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


            $this->reply($user,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->raze($user, $building, $amount)));
        }


    }
}