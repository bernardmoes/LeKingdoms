<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 21:49
 */
class Report extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $r = $this->q("SELECT value FROM worldvars WHERE name='ageof';");
        $ageof = $r['value'];
        $r = $this->q("SELECT value FROM worldvars WHERE name='turns';");
        $turn = $r['value'];
        $this->lastturn = $turn;
        $report = $this->q("SELECT report, timestamp FROM reports WHERE user = UNHEX('" . bin2hex($user) . "');");
//			if (!$report)  $this->reply($user,$p, "no reports yet. wait for the world to turn");
        $timepassed = time() - intval($report['timestamp']);
        $timepassed =  $timepassed / 3600;
        return $this->reply($user,$p, "You are playing Kingdoms, the age of " . $ageof . ", the world is " . $turn . " turns old. Your last report was generated " . (  intval($report['timestamp']) == 0 ? '... never as you are yet to play through a turn.' : round($timepassed * 10)/10 . " hours ago:\n" . $report['report']));
    }
}