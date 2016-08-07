<?php
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Annex extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) <= 1) {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), "you can annex some unused surrounding land by !annex nn:mm, where nn:mm is the location of the land. this costs " . COST_TO_ANNEX . " gc for surveying.");
        } else {
            $location =  preg_replace('/[^0-9:]+/sm', '', $c[1]);
            return $this->__communicator->sendReply($this->__message->getAuthorName(), KingdomHelper::annex(clean($this->__message->getAuthorName()), $location));
        }
    }
}