<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Buildings extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $report = "buildings:\n";

        foreach (KingdomHelper::$buildings_key as $k => $v) {
            $costs = array();
            if (KingdomHelper::$buildings[$k]['wo'] > 0) $costs[] = KingdomHelper::$buildings[$k]['wo'] . " wood";
            if (KingdomHelper::$buildings[$k]['r'] > 0) $costs[] = KingdomHelper::$buildings[$k]['r'] . " stone";
            if (KingdomHelper::$buildings[$k]['i'] > 0) $costs[] = KingdomHelper::$buildings[$k]['i'] . " iron";
            if (KingdomHelper::$buildings[$k]['g'] > 0) $costs[] = KingdomHelper::$buildings[$k]['g'] . " gc";
            $report .= $v . " ( " .  implode(", ", $costs) . ' ) : ' . KingdomHelper::$buildings[$k]['d'] . ".\n";
        }
        $this->__communicator->sendReply($this->__message->getAuthorName(), $report);
    }
}