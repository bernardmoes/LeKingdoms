<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Bernanke extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $this->__communicator->sendPublic($this->__message->getAuthorName() ." obtains 1000 gc for doing nothing");
        $k = $this->__db->getKingdom(clean($this->__message->getAuthorName()));
        $k['G'] += 1000;
        $this->__db->saveKingdom($k);
    }
}