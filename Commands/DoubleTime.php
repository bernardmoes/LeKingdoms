<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 21:35
 */

class DoubleTime extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        if (count($c) != 2) return $this->reply($user,$p, "you mean !doubletime username");

        $alreadyattacked = $this->q("DELETE FROM spells WHERE castby = \"sythe\" AND caston = \"" . clean($c[1]) . "\" AND spell = \"(attacked)\" LIMIT 1;");
        return $this->reply($user,$p, "sythe doubletimed " . $c[1] . ".");
    }
}


