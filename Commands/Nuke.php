<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Nuke extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $this->q("DELETE FROM kingdom;");
        $this->q("DELETE FROM turnnotes;");
        $this->q("DELETE FROM spells;");
        $this->q("DELETE FROM reports;");
        $this->q("DELETE FROM items;");
        $this->q("UPDATE worldvars SET value = 0 WHERE name = 'turns';");
        array_shift($c);
        $text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));

        $this->q("UPDATE worldvars SET value = UNHEX('" . bin2hex($text) . "') where name = 'ageof';");
        $this->room("sythe nuked the entire world. we welcome the new age of " . $text);
    }
}