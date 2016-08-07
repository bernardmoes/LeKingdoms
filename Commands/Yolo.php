<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:45
 */
class Yolo extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function yoloNow($u)
    {
        // check enough food
        $d = $this->__db->getKingdom($u);

        $popcap = round(PEOPLE_PER_HOUSE * $d['H']);
        $wineneeded =  ($d['P'] *5 +1);
        $foodneeded =  ($d['P'] *4 +1);

        if ($d['F'] < $foodneeded) return "your people cannot have an orgy without a sufficient feast first. you need at least " . $foodneeded . " bushels of food.";

        if ($d['WA'] < $wineneeded)  return "your people cannot have an orgy without a sufficient drink first. you need at least " . $wineneeded . " liters of water.";

        $newpeople = round(rand($d['P']/1.5, $d['P'] * 1.5)  / 3);

        if ($newpeople + $d['P'] > $popcap) return "your people cannot breed because you lack housing.";

        //$alreadyyolo = $this->__db->executeQuery("SELECT * FROM spells WHERE castby = \"" . clean($u) . "\" AND caston = \"" . clean($u) . "\" AND spell = \"(yolo)\" LIMIT 1;");
        //if ($alreadynewplayer) {
        //	 return "you've yolo'ed once this turn. your people are orgyed out and cannot be compelled to breed further until next turn";
        //} else {

        $d['P'] += $newpeople;
        $d['F'] -= $foodneeded;
        $d['WA'] -= $wineneeded;

        $report = "your people throw a massive feast, consuming " . $wineneeded . " flagons of wine and " . $foodneeded . " servings of food. after eating they retire to the orgarium for dirty cuddles. " . $newpeople . " new people were subsequently added to your kingdom's population.";

        $this->__db->saveKingdom($d);
        //	$this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"(yolo)\", 1);");

        $this->__communicator->sendReply($this->__message->getAuthorName(), $report);
    }

    function execute()
    {

        $this->__communicator->sendReply($this->__message->getAuthorName(), $this->yoloNow(clean($this->__message->getAuthorName())));
    }
}