<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Cash extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("you have %s gc in your coffers", $this->__kingdom['G']));
    }
}