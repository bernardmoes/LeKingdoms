<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Help extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $this->__communicator->sendReply($this->__message->getAuthorName(), "just some of the commands: !stats !players !build !attack !annex !cast !trade !prices !spells !espionage !thieve and others. see play guide for details: http://www.sythe.org/threads/le-kingdoms-gameplay-manual.1601391/");
    }
}