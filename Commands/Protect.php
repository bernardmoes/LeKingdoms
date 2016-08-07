<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Protect extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) != 3) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !protect username turns");
        $k = $this->__db->getKingdom(clean($c[1]));
        if (!$k) return $this->__communicator->sendReply($this->__message->getAuthorName(),"user " . $c[1] . " does not have a kingdom");

        $turns = intval($c[2]);
        if ($turns <= 0) $turns = 1;
        $this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($c[1]) . "\", \"" . clean($c[1]) . "\", \"protection\", " . intval($turns) . ") ON DUPLICATE KEY UPDATE duration = " . intval($turns) . ";");

        return $this->__communicator->sendPublic($this->__message->getAuthorName() . " cast protection on " . $c[1] . " for " . intval($turns) . " turns.");
    }
}