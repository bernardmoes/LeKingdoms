<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Abra extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) != 5) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !abra soldiers weapons horses battlements");
        $k = $this->__db->getKingdom(clean($this->__message->getAuthorName()));

        $k['S'] = intval($c[1]);
        $k['W'] = intval($c[2]);
        $k['HO'] = intval($c[3]);
        $k['BT'] = intval($c[4]);

        $this->__db->saveKingdom($k);

        return $this->__communicator->sendPublic($this->__message->getAuthorName() . " magicked himself an army.");
    }
}