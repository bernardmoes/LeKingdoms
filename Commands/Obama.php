<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Obama extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $this->room( "sythe set up an obama-matic printing press and fed the world's remaining common sense and decency into it, producing 1000000 gc");
        $k = $this->get_kingdom("sythe");
        $k['G'] += 1000000;
        $this->save_kingdom($k);
    }
}