<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 21:35
 */

class DoubleTime extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) != 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !doubletime username");

        $alreadyattacked = $this->__db->executeQuery("DELETE FROM spells WHERE castby = \"sythe\" AND caston = \"" . clean($c[1]) . "\" AND spell = \"(attacked)\" LIMIT 1;");
        return $this->__communicator->sendReply($this->__message->getAuthorName(), "sythe doubletimed " . $c[1] . ".");
    }
}


