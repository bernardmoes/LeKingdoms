<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Spells extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $report = "spells:\n";
        foreach(Kingdom::$spells as $s => $a) {
            $report .= $s . " costs " . $a['r'] . " runes and lasts " . $a['l'] . " turns and " . $a['d'] . "\n";
        }
        return $this->__communicator->sendReply($this->__message->getAuthorName(), $report);

    }
}