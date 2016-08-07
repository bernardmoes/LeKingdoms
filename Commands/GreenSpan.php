<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class GreenSpan extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $this->__communicator->sendPublic($this->__message->getAuthorName() . " obtains 10000 gc for doing nothing");
        $k = $this->__db->getKingdom(clean($this->__message->getAuthorName()));
        $k['G'] += 10000;
        $this->__db->saveKingdom($k);
    }
}