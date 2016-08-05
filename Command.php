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
    public function __construct($message, $kingdom, $communicator)
    {
        $this->__message = $message;
        $this->__kingdom = $kingdom;
        $this->__db = DBCommunicator::getInstance();
        $this->__communicator = $communicator;
    }

    abstract function execute();
}