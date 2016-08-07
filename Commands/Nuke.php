<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Nuke extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        $this->__db->executeQuery("DELETE FROM kingdom;");
        $this->__db->executeQuery("DELETE FROM turnnotes;");
        $this->__db->executeQuery("DELETE FROM spells;");
        $this->__db->executeQuery("DELETE FROM reports;");
        $this->__db->executeQuery("DELETE FROM items;");
        $this->__db->executeQuery("UPDATE worldvars SET value = 0 WHERE name = 'turns';");
        array_shift($c);
        $text = preg_replace('/[^a-zA-Z0-9: .\-,]/m', '', implode(" ", $c));

        $this->__db->executeQuery("UPDATE worldvars SET value = UNHEX('" . bin2hex($text) . "') where name = 'ageof';");
        $this->__communicator->sendPublic("Sythe nuked the entire world. we welcome the new age of " . $text);
    }
}