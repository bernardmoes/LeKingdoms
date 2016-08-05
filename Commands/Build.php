<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:41
 */
class Build extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function buildNow()
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

    function execute()
    {
        if (count($c) <= 1) {
            $this->reply($user,$p, "try !build [building type] [amount]. for a list of buildings try !buildings");
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


            $this->reply($user,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->build($user, $building, $amount)));
        }
    }
}

class BuildMax extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function buildMaxNow()
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

    function execute()
    {
        if (count($c) <= 1) {
            $this->reply($user,$p, "try !buildmax [building type]. for a list of buildings try !buildings");
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

            $this->reply($user,$p, ($building === false ? "invalid building type specified. try !buildings for a list of valid buildings" : $this->buildmax($user, $building)));
        }
    }
}