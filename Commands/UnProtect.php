<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class UnProtect extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) != 2) return$this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !unprotect username");
        $k = $this->__db->getKingdom(clean($c[1]));
        if (!$k) return $this->__communicator->sendReply($this->__message->getAuthorName(),"user " . $c[1] . " does not have a kingdom");

        $this->__db->executeQuery("DELETE FROM spells WHERE caston = \"" . clean($c[1]) . "\", AND spell = \"protection\" LIMIT 1;");

        return $this->__communicator->sendPublic($this->__message->getAuthorName() . " removed protection from " . $c[1]);
    }
}