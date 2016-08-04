<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Dice extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        if (count($c) > 2) return $this->reply($user,$p, "you mean !dice [face count]");
        $faces = 6;
        if (count($c) == 2) $faces = abs(intval($c[1]));

        if ($faces <= 1) $faces = 6;
        return $this->reply($user,$p, "dice roll: " . (rand(0, $faces - 1) + 1) . " of " . $faces);

    }
}