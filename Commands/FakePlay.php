<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class FakePlay extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $this->new_player("fakeplayer" . rand(100,10000));
    }
}