<?php
require_once "./Models/OutputMessage.php";
require_once "Communicator.php";
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 5-8-2016
 * Time: 13:51
 */
class DiscordCommunicator extends Communicator
{
    private $_privateChannel;
    private $_publicChannel;
    private $_communicator;

    public function __construct($private, $public)
    {
        parent::__construct();
        $this->_privateChannel = $private;
        $this->_publicChannel = $public;
    }

    function update(OutputMessage $message)
    {
        switch($message->getMessageType())
        {
            case 'pm':
                $this->sendPM($message->getReplyTo(), $message->getMessage());
                break;
            case 'public':
                $this->sendPublic($message->getMessage());
                break;
            case 'both':
                $this->sendBoth($message->getReplyTo(), $message->getMessage());
                break;
            case 'reply':
                $this->sendReply($message->getReplyTo(), $message->getMessage());
                break;
        }
    }

    public function sendPM($user, $message) {
        $this->_privateChannel->sendMessage("```@" . $user . ": " . $message . '```');
    }

    public function sendPublic($message) {
        $this->_publicChannel->sendMessage('```' . $message . '```');
    }

    public function sendBoth($user, $message) {
        $this->_publicChannel->sendMessage('```' . $message . '```');
        if ($this->_privateChannel->channel_id == $this->_publicChannel->channel_id) return;
        $this->_privateChannel->sendMessage("```@" . $user . ": " . $message . '```');
    }

    public function sendReply($user, $message) {
       $this->sendPM($user, $message);
    }
}