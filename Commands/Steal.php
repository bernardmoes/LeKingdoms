<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Steal extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function stealNow($loc, $u)
    {
        $loc = clean($loc);
        $u = clean($u);

        $victim = KingdomHelper::get_username_at_location($loc);
        if ($victim === false) return "cannot thieve from " . $loc . " as no kingdom exists there.";

        $v = $this->__db->getKingdom($victim);
        $a = $this->__db->getKingdom($u);

        if ($v['username'] == $a['username']) return "you cannot thieve from yourself!";

        if ($a['T'] == 0) return "you cannot thieve until you have at least one thieves den. try !build thieves den";

        $alreadythieved = $this->__db->executeQuery("SELECT * FROM spells WHERE castby = \"" . clean($a['username']) . "\" AND caston = \"" . clean($v['username']) . "\" AND spell = \"(thieved)\" LIMIT 1;")->fetchAll(PDO::FETCH_ASSOC);
        if ($alreadythieved) return "you've already thieved " . $v['username'] . " this turn. your thieves are enjoying their spoils and cannot be persuaded to thieve this kingdom again until next turn";

        $ratio =  ($v['T']*1.2 + 1) / ($a['T'] + 1);

        $report = "your thieves snuck into " . $v['username'] . "'s kingdom. ";

        $steal = 0;
        if ($ratio < 0.25) {
            $report .= "they stole much, as the enemy had relatively few of its own thieves to keep you out. ";

            $steal = rand(0, 30);
        } else if ($ratio < 0.75) {
            $report .= "their reach was limited due being detected by the enemy's own thieves. ";
            KINGDOMHELPER::add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
            $steal = rand(0, 10);
        } else if ($ratio < 1) {
            $report .= "they were outnumbered by the enemy's own thieves. ";
            $steal = rand(0, 5);
            // third best
        } else if ($ratio < 10) {
            $report .= "they were vastly outnumbered by the enemy's own thieves. ";
            $steal = rand(0, 1);
            // minimal
        }

        $steal = round(($steal/100) * $v['G']);
        $report .= "we stole: " . $steal . " gc from the enemy. this gold has been added to our coffers";
        $v['G'] -= $steal;
        if ($v['G'] < 0) $v['G'] = 0;
        $a['G'] += $steal;

        $this->__db->saveKingdom($v);
        $this->__db->saveKingdom($a);

        $this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($a['username']) . "\", \"" . clean($v['username']) . "\", \"(thieved)\", 1);");

        return $report;
    }

    function execute()
    {
        if (count($this->__message->getContentArgs()) < 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can use your thieves dens to !thieve nn:mm or !thieve username other kingdoms");
        $loc =  KingdomHelper::resolve_location_from_input($this->__message->getContentArgs()[1]);
        if ($loc === false) return $this->__communicator->sendReply($this->__message->getAuthorName(), "cannot thieve from " . $this->__message->getContentArgs()[1] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
        $this->__communicator->sendReply($this->__message->getAuthorName(), $this->stealNow($loc, $this->__message->getAuthorName()));
    }
}