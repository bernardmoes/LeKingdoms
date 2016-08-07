<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Items extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $result = $this->__db->executeQuery("SELECT item, amountleft FROM items WHERE kingdom = \"" . clean($this->__message->getAuthorName()) . "\";")->fetchAll(PDO::FETCH_ASSOC);
        $report = '';

        if ($result && count($result) > 0) {
            $report .= "your kingdom has the following magical items in its possession:\n";
            foreach($result as $notes)
            {
                $report .=  $notes['item'] . ' with ' . $notes['amountleft'] . ' uses remaining'. "\n";;
            }
        } else {
            $report .= "your kingdom possesses no magical items at this time.";

        }
        return $this->__communicator->sendReply($this->__message->getAuthorName(), $report);
    }
}