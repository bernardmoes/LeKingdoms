<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Protect extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) != 3) return $this->reply($user,$p, "you mean !protect username turns");
        $k = $this->get_kingdom(clean($c[1]));
        if (!$k) return $this->reply($user,$p,"user " . $c[1] . " does not have a kingdom");

        $turns = intval($c[2]);
        if ($turns <= 0) $turns = 1;
        $this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($c[1]) . "\", \"" . clean($c[1]) . "\", \"protection\", " . intval($turns) . ") ON DUPLICATE KEY UPDATE duration = " . intval($turns) . ";");

        return $this->room( "sythe cast protection on " . $c[1] . " for " . intval($turns) . " turns.");
    }
}