<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Obliterate extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function oblitarateNow($u, $cloc)
    {
        $cloc =  explode(":", preg_replace('/[^0-9\:]+/sm', '', $cloc));
        $u = clean($u);
        $d = $this->__db->getKingdom($u);

        if (count($cloc) != 2) return "please use the command like this !annex number:number";

        $cloc[0] = intval($cloc[0]);
        $cloc[1] = intval($cloc[1]);

        if ($cloc[0] < 0 || $cloc[0] > MAP_SIZE || $cloc[1] < 0 || $cloc[1] > 100) return "the specified land is outside the bounds of the world, sorry.";


        $playerlocs = KingdomHelper::make_loc_array($d['locations']);

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
                    $report[] = ($v - $newitem) . " " . KingdomHelper::translate($k);
                }
                $d[$k] = $newitem;
            }
        }

        $this->__db->saveKingdom($d);

        return "your army sets fires across your land completely obliterating " . $cloc[0] . ":" . $cloc[1] . " and " . implode(", ", $report);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) <= 1) {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can obliterate some of your lands like so !obliterate nn:mm");
        } else {
            $location =  preg_replace('/[^0-9:]+/sm', '', $c[1]);
            return $this->__communicator->sendReply($this->__message->getAuthorName(), $this->oblitarateNow(clean($this->__message->getAuthorName()), $location));
        }
    }
}



