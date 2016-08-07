<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Obama extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $this->__communicator->sendPublic($this->__message->getAuthorName() ." set up an obama-matic printing press and fed the world's remaining common sense and decency into it, producing 1000000 gc");
        $k = $this->__db->getKingdom(clean($this->__message->getAuthorName()));
        $k['G'] += 1000000;
        $this->__db->saveKingdom($k);
    }
}