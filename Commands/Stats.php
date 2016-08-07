<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Stats extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        $n = (count($c) > 1 ? clean($c[1]) : clean($this->__message->getAuthorName()) );
        $d = $this->__db->getKingdom($n);
        if ($d === false && $n == $this->__message->getAuthorName()) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have a kingdom, what are you poor? maybe try !play");
        else if ($d === false) return  $this->__communicator->sendReply($this->__message->getAuthorName(), $n . " doesn't have a kingdom. you should invite him to !play");

        $this->__communicator->sendReply($this->__message->getAuthorName(), KingdomHelper::printKingdom($d));
    }
}