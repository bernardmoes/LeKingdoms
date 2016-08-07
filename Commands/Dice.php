<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Dice extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        if (count($this->__message->getContentArgs()) > 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !dice [face count]");
        $faces = 6;
        if (count($this->__message->getContentArgs()) == 2) $faces = abs(intval($this->__message->getContentArgs()[1]));

        if ($faces <= 1) $faces = 6;
        $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("dice roll: %s of %s", (rand(0, $faces - 1) + 1), $faces));

    }
}