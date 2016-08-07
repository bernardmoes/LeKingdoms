<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class CheckTurn extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='turns';")->fetch(PDO::FETCH_ASSOC);;
        $turn = $r['value'];
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='lastturn';")->fetch(PDO::FETCH_ASSOC);;
        $lastturntime = intval($r['value']);
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='turnfreq';")->fetch(PDO::FETCH_ASSOC);;
        $turnfreq = intval($r['value']);
        $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("<C>%s", ($turnfreq - (time() - $lastturntime))));
        if ($this->lastturntime <> $turn) {
            $this->__communicator->sendReply($this->__message->getAuthorName(), "the world has turned! Go ahead and post !report");
        }
    }
}