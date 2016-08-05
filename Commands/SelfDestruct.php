<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class SelfDestruct extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $this->q('DELETE FROM kingdom WHERE username = "' . clean($user) . '" LIMIT 1;');
        $this->reply($user,$p, "kingdom of " . $user . " ceased to exist. to start over !play");
    }
}