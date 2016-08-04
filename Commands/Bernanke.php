<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Bernanke extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $this->room("sythe obtains 1000 gc for doing nothing");
        $k = $this->get_kingdom("sythe");
        $k['G'] += 1000;
        $this->save_kingdom($k);
    }
}