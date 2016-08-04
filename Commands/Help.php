<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Help extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $this->reply($user,$p, "just some of the commands: !stats !players !build !attack !annex !cast !trade !prices !spells !espionage !thieve and others. see play guide for details: http://www.sythe.org/threads/le-kingdoms-gameplay-manual.1601391/");
    }
}