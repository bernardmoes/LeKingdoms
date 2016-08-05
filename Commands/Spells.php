<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Spells extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $report = "spells:\n";
        foreach(self::$spells as $s => $a) {
            $report .= $s . " costs " . $a['r'] . " runes and lasts " . $a['l'] . " turns and " . $a['d'] . "\n";
        }
        return $this->reply($user,$p, $report);

    }
}