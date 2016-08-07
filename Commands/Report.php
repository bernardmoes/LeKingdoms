<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 21:49
 */
class Report extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='ageof';")->fetch(PDO::FETCH_ASSOC);
        $ageof = $r['value'];
        $r = $this->__db->executeQuery("SELECT value FROM worldvars WHERE name='turns';")->fetch(PDO::FETCH_ASSOC);;
        $turn = $r['value'];
        $this->lastturn = $turn;
        $report = $this->__db->executeQuery("SELECT report, timestamp FROM reports WHERE user = UNHEX('" . bin2hex($this->__message->getAuthorName()) . "');")->fetch(PDO::FETCH_ASSOC);;
        //if (!$report)  $this->__communicator->sendReply($this->__message->getAuthorName(), "no reports yet. wait for the world to turn");
        $timepassed = time() - intval($report['timestamp']);
        $timepassed =  $timepassed / 3600;
        $reportExists = !(intval($report['timestamp']) == 0);
        if($reportExists)
        {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("You are playing Kingdoms, the age of %s, the world is %s turns old. Your last report was generated %s hours ago:\n %s", $ageof, $turn, round($timepassed * 10)/10, $report['report']));
        } else {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("You are playing Kingdoms, the age of %s, the world is %s turns old.  Your last report was generated ... never as you are yet to play through a turn.", $ageof, $turn));
        }
    }
}