<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 5-8-2016
 * Time: 13:11
 */


class OutputMessage
{
    public static $messageTypes = array(
        0 => 'pm',
        1 => 'public',
        2 => 'both',
        3 => 'reply'
    );

    private $_messageType;
    private $_message;
    private $_replyTo;

    public function __construct($messageType, $message, $replyTo = null)
    {
        if(in_array($messageType, OutputMessage::$messageTypes))
        {
            $this->_messageType = array_search($messageType, OutputMessage::$messageTypes);
        }

        $this->_message = $message;
        $this->_replyTo = $replyTo;
    }

    public function getMessageType()
    {
        return OutputMessage::$messageTypes[$this->_messageType];
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function getReplyTo()
    {
        return $this->_replyTo;
    }
}