<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Abra extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) != 5) return $this->reply($user,$p, "you mean !abra soldiers weapons horses battlements");
        $k = $this->get_kingdom(clean($user));

        $k['S'] = intval($c[1]);
        $k['W'] = intval($c[2]);
        $k['HO'] = intval($c[3]);
        $k['B'] = intval($c[4]);

        $this->save_kingdom($k);

        return $this->room( "sythe magicked himself an army.");
    }
}