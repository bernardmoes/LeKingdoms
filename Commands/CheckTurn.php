<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class CheckTurn extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $r = $this->q("SELECT value FROM worldvars WHERE name='turns';");
        $turn = $r['value'];
        $r = $this->q("SELECT value FROM worldvars WHERE name='lastturn';");
        $lastturntime = intval($r['value']);
        $r = $this->q("SELECT value FROM worldvars WHERE name='turnfreq';");
        $turnfreq = intval($r['value']);
        $this->reply($user,$p, "<C>" . ($turnfreq - (time() - $lastturntime)));
        if ($this->lastturn <> $turn) {
            $this->reply($user,$p, "the world has turned! here's your report:");
            return $this->process_command("report", $user, $type);
        }
    }
}