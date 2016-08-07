<?php
require_once "DBCommunicator.php";
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:36
 */
abstract class Command
{
    /** @var DiscordMessage */
    protected $__message;
    /** @var DBCommunicator */
    protected $__db;
    protected $__kingdom;
    /** @var Communicator */
    protected $__communicator;
    /** @var CommandEvaluator */
    protected $__commandEvaluator;
    public function __construct(CommandEvaluator $evaluator)
    {
        $this->__message = $evaluator->getMessage();
        $this->__kingdom = $evaluator->getKingdom();
        $this->__db = DBCommunicator::getInstance();
        $this->__communicator = $evaluator->getCommunicator();
        $this->__commandEvaluator = $evaluator;
    }

    abstract function execute();
}