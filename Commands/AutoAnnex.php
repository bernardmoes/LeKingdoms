<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 7-8-2016
 * Time: 20:32
 */
class AutoAnnex extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function autoAnnex($u)
    {
        $u = clean($u);

        $k = $this->__db->getKingdom($u);

        $locations = KingdomHelper::make_loc_array($k['locations']);

        foreach($locations as $k => $l) {

            $loc = explode(":", $l);

            $x = $loc[0];
            $xmin = $loc[0] - 1; if ($xmin < 0) $xmin == 0;
            $xmax = $loc[0] + 1; if ($xmax > MAP_SIZE) $xmax == MAP_SIZE;

            $y = $loc[1];
            $ymin = $loc[1] - 1; if ($ymin < 0) $ymin == 0;
            $ymax = $loc[1] + 1; if ($ymax > MAP_SIZE) $ymax == MAP_SIZE;

            $msg = "the specified land is outside the bounds of the world";

            if (KingdomHelper::get_username_at_location($xmin . ":" . $ymin) === false) $msg = KingdomHelper::annex($u, $xmin . ":" . $ymin);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($xmin . ":" . $ymax) === false) $msg = KingdomHelper::annex($u, $xmin . ":" . $ymax);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($xmax . ":" . $ymin) === false) $msg = KingdomHelper::annex($u, $xmax . ":" . $ymin);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($xmax . ":" . $ymax) === false) $msg = KingdomHelper::annex($u, $xmax . ":" . $ymax);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($x . ":" . $ymin) === false) $msg = KingdomHelper::annex($u, $x . ":" . $ymin);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($xmin . ":" . $y) === false) $msg = KingdomHelper::annex($u, $xmin . ":" . $y);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($xmax . ":" . $y) === false) $msg = KingdomHelper::annex($u, $xmax . ":" . $y);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

            if (KingdomHelper::get_username_at_location($x . ":" . $ymax) === false) $msg = KingdomHelper::annex($u, $x . ":" . $ymax);
            if (strrpos($msg, "the specified land is outside the bounds of the world") === false) return $msg;

        }

        return "no free space around your kingdom to annex!";
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        $times = 1;
        if (count($c) == 2) $times = abs(intval($c[1]));
        $messages = array();

        if ($times > 20) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can only autoannex at most 20 squares of land at a time");

        for ($i = 0; $i < $times; $i++) {
            $latest = $this->autoannex(clean($this->__message->getAuthorName()));
            $messages[] =  $latest;
            if (strrpos($latest, "you have insufficent") !== false) break;
        }

        return $this->__communicator->sendReply($this->__message->getAuthorName(), implode("\n", $messages));
    }
}