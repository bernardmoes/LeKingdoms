<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Stats extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $n = (count($c) > 1 ? clean($c[1]) : clean($user) );
        $d = $this->get_kingdom($n);
        if ($d === false && $n == $user) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have a kingdom, what are you poor? maybe try !play");
        else if ($d === false) return  $this->__communicator->sendReply($this->__message->getAuthorName(), $n . " doesn't have a kingdom. you should invite him to !play");

        $this->__communicator->sendReply($this->__message->getAuthorName(), $this->print_kingdom($d));
    }
}