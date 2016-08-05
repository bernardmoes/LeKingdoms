<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Space extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("you have %s acres of spare land in your kingdom", $this->__kingdom['L']));
    }
}