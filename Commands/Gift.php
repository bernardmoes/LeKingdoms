<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Gift extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) < 3) return $this->__communicator->sendReply($this->__message->getAuthorName(),"try !gift yourfriend 500");
        $k = $this->get_kingdom($user);
        $f = $this->get_kingdom($c[1]);

        if($k === false) 	return $this->__communicator->sendReply($this->__message->getAuthorName(),"cannot !gift if you don't have a kingdom. try !play");
        if($f === false) return $this->__communicator->sendReply($this->__message->getAuthorName(),$c[1] . " does not have a kingdom");

        if ($k['username'] == $f['username']) return $this->__communicator->sendReply($this->__message->getAuthorName(),"you cannot gift to yourself");
        $amount = abs(intval($c[2]));
        if ($k['G'] < $amount) return $this->__communicator->sendReply($this->__message->getAuthorName(),"you do not have sufficient gc to gift this amount");
        $k['G'] -= $amount;
        $f['G'] += $amount;

        $this->save_kingdom($k);
        $this->save_kingdom($f);
        return $this->room($user . " gifted " . $amount . " gc to " . $c[1]);

    }
}