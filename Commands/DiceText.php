<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class DiceText extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($this->__message->getContentArgs()) < 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you mean !dicetext option1,option2,option3");

        $faces = count($this->__message->getContentArgs());
        $c = $this->__message->getContentArgs();
        unset($c[0]);

        $text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));
        $options = explode(",",$text);

        $faces = count($options);

        $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("-> %s", $options[(rand(0, $faces -1))]));
    }
}