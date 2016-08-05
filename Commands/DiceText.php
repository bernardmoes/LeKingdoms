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
        if (count($c) < 2) return $this->reply($user,$p, "you mean !dicetext option1,option2,option3");

        $faces = count($c);

        unset($c[0]);

        $text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));
        $options = explode(",",$text);

        $faces = count($options);

        return $this->reply($user,$p, "-> " . $options[(rand(0, $faces -1))]);


    }
}