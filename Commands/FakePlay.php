<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class FakePlay extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        (new Play($this->__commandEvaluator))->createFakePlayer("fakeplayer" . rand(100,10000));
    }
}