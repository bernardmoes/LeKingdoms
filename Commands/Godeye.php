<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Godeye extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) != 2) return $this->reply($user,$p, "you mean !godeye username");
        $k = $this->get_kingdom(clean($c[1]));
        if (!$k) return $this->reply($user,$p,"user " . $c[1] . " does not have a kingdom");

        return $this->reply($user,$p,$this->print_kingdom(  $k  ));
    }
}