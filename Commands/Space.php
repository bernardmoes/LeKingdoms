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
        $k = $this->get_kingdom($user);
        if ($k === false) return $this->reply($user,$p, "you don't own a kingdom. try !play");
        return $this->reply($user,$p, "you have " . $k['L'] . " acres of spare land in your kingdom");
    }
}