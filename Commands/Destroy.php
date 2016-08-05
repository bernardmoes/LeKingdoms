<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Destroy extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $this->q('DELETE FROM kingdom WHERE username = "' . clean($c[1]) . '" LIMIT 1;');
        $this->reply($user,$p, "kingdom of " . clean($c[1]) . " has been razed to the ground!");
    }
}