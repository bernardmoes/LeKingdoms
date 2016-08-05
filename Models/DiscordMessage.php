<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 17:31
 */
class DiscordMessage
{
    private $_ID;
    private $_channelID;
    private $_content;
    private $_type;
    private $_authorID;
    private $_authorName;
    private $_date;
    private $_isAdmin;
    private $_isAuthorizedBot;
    private $_contentArgs;
    private $_shouldProcess;

    public function __construct($message)
    {
        $content = strtolower(str_replace("\r", "", str_replace("\n", "", $message->content)));
        $this->_shouldProcess = (strlen($content) > 1 && $content[0] == '!');
        $content = str_replace("!", "", $content);
        $this->_ID = $message->id;
        $this->_channelID = $message->channel_id;
        $this->_content = $content;
        $this->_type = $message->type;
        $this->_authorID = $message->author->id;
        $this->_authorName = $message->author->username;
        $this->_date = $message->timestamp;
        $this->_isAdmin = ($message->author->id == ADMIN1 || $message->author->id == ADMIN2);
        $this->_isAuthorizedBot = ($message->author->id == AUTHORIZEDBOT);

        $this->_contentArgs = explode(' ', $content);
        //TODO: PREG REPLACE USER INPUT
    }

    public function getID()
    {
        return $this->_id;
    }

    public function getChannelID()
    {
        return $this->_channelID;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function getContentArgs()
    {
        return $this->_contentArgs;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getAuthorID()
    {
        return $this->_authorID;
    }

    public function getAuthorName()
    {
        return $this->_authorName;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function shouldProcess()
    {
        return $this->_shouldProcess;
    }

    public function isAdmin()
    {
        return $this->_isAdmin;
    }

    public function isAuthorizedBot()
    {
        return $this->_isAuthorizedBot;
    }

}

