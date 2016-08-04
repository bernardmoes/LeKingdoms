<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Cash extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $k = $this->get_kingdom($user);
        if ($k === false) return $this->reply($user,$p, "you don't own a kingdom. try !play");
        return $this->reply($user,$p, "you have " . $k['G'] . " gc in your coffers");

    }
}