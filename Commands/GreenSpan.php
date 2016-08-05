<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class GreenSpan extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $this->room("sythe obtains 10000 gc for doing nothing");
        $k = $this->get_kingdom("sythe");
        $k['G'] += 10000;
        $this->save_kingdom($k);
    }
}