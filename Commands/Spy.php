<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Spy extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function spyNow()
    {
        $loc = clean($loc);
        $u = clean($u);



        $victim = $this->get_username_at_location($loc);
        if ($victim === false) return "cannot spy on " . $loc . " as no kingdom exists there.";

        $v = $this->get_kingdom($victim);
        $a = $this->get_kingdom($u);

        if ($v['username'] == $a['username']) return "you cannot spy on yourself. you could always use !stats";

        if ($a['IA'] == 0) return "you cannot spy until you have at least one intelligence agency. try !build intelligence agency";

        $ratio =  ($v['IA'] + 1) / ($a['IA'] + 1);

        $report = "your spies entered " . $v['username'] . "'s kingdom by night. ";


        if ($ratio < 0.25) {
            $report .= "their job was ridiculously easy as the enemy had relatively few of its own spies. ";
            $report .= $this->print_kingdom($v);
        } else if ($ratio < 0.75) {
            $report .= "they were able only to obtain limited information due the enemy's own spies. ";
            $report .= $v['username'] . "'s kingdom had:\nunused land: " . $v['L'] . ", gc: " . $v['G'] . ", food stocks: " . $v['F'] . ", soldiers: " . $v['S'] . ", civilians: " . $v['P'] . ", iron: " . $v['I'] . ", magic runes: " . $v['M']  . ", this was all the information we could obtain with our current level of espionage ability";
            $this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
        } else if ($ratio < 1) {
            $report .= "they were outnumbered by the enemy's own spies and obtained very little information. ";
            $report .= $v['username'] . "'s kingdom had:\nsoldiers: " . $v['S'] . ", civilians: " . $v['P'] . ", iron: " . $v['I'] . ", magic runes: " . $v['M']  . ", this was all the information we could obtain with our current level of espionage ability";
            $this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
            // third best
        } else if ($ratio < 10) {
            $report .= "they were vastly outnumbered by the enemy's own spies and obtained very little information. ";
            $report .= $v['username'] . "'s kingdom had:\nsoldiers: " . $v['S'] . ", this was all the information we could obtain with our current level of espionage ability";
            $this->add_turn_note($a['username'], $v['username'], $a['username'] . "'s spys were caught in our kingdom");
            // minimal
        }



        return $report;
    }

    function execute()
    {
        if (count($c) < 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can use your intelligence agencies to !espionage nn:mm or !espionage username other kingdoms");
        $loc =  $this->resolve_location_from_input($c[1]);
        if ($loc === false) return $this->__communicator->sendReply($this->__message->getAuthorName(), "cannot spy on " . $c[1] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
        $this->__communicator->sendReply($this->__message->getAuthorName(), $this->espionage($loc, $user));

    }
}