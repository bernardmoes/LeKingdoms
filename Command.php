<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:36
 */
abstract class Command
{
    private $_message;
    private $_user;
    public function __construct($message, $user)
    {
        $this->_message = $message;
        $this->_user = $user;
    }

    abstract function execute();
}