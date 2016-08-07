<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class SelfDestruct extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $this->__db->executeQuery('DELETE FROM kingdom WHERE username = "' . clean($this->__message->getAuthorName()) . '" LIMIT 1;');
        $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("kingdom of %s ceased to exist. to start over !play", $this->__message->getAuthorName()));
    }
}