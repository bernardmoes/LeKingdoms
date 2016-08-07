<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Space extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("you have %s acres of spare land in your kingdom", $this->__kingdom['L']));
    }
}