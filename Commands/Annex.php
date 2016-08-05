<?php
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Annex extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function annex()
    {
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
        $this->__db->executeQuery("UPDATE kingdom SET G=" . intval($gold) . ", L=" . intval($land) . ", locations=\"" . $locations . "\" WHERE username =\"" . clean($u) . "\" LIMIT 1;");

        return "land at " . $cloc[0] . ":" . $cloc[1] .  " annexed!";
    }

    function execute()
    {
        if (count($c) <= 1) {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can annex some unused surrounding land by !annex nn:mm, where nn:mm is the location of the land. this costs " . self::$CTA . " gc for surveying.");
        } else {
            $location =  preg_replace('/[^0-9:]+/sm', '', $c[1]);
            return $this->reply($user, $p, $this->annex(clean($user), $location));
        }
    }
}

class AutoAnnex extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function autoAnnex()
    {
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

    function execute()
    {
        $times = 1;
        if (count($c) == 2) $times = abs(intval($c[1]));
        $messages = array();

        if ($times > 20) return $this->reply($user, $p, "you can only autoannex at most 20 squares of land at a time");

        for ($i = 0; $i < $times; $i++) {
            $latest = $this->autoannex(clean($user));
            $messages[] =  $latest;
            if (strrpos($latest, "you have insufficent") !== false) break;
        }

        return $this->reply($user, $p, implode("\n", $messages));
    }
}