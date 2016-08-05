<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class CheckTurn extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='turns';");
        $turn = $r['value'];
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='lastturn';");
        $lastturntime = intval($r['value']);
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='turnfreq';");
        $turnfreq = intval($r['value']);
        $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("<C>%s", ($turnfreq - (time() - $lastturntime))));
        if ($this->lastturn <> $turn) {
            $this->__communicator->sendReply($this->__message->getAuthorName(), "the world has turned! Go ahead and post !report");
        }
    }
}