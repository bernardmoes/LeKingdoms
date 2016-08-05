<?php
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 5-8-2016
 * Time: 13:03
 */
abstract class Communicator
{
    public function __construct()
    {

    }

    public abstract function sendPublic($message);
    public abstract function sendPM($user, $message);
    public abstract function sendBoth($user, $message);
    public abstract function sendReply($user, $message);


}