<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 5-8-2016
 * Time: 11:45
 */
class DBCommunicator
{
    /** @var DBCommunicator */
    private static $_instance;
    private $_db;

    private function __construct()
    {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('couldnt connect');
    }

    public static function getInstance()
    {
        if(DBCommunicator::$_instance == null)
        {
            DBCommunicator::$_instance = new DBCommunicator();
        }
        return DBCommunicator::$_instance;
    }

    public function executeQuery($query)
    {
        $result = $this->db->query($query);

        if($result && $result->num_rows > 0) {
            if($row = $result->fetch_assoc()) {
                return $row;
            }
        }
        return false;
    }

    public function getKingdom($u) {
        return $this->executeQuery(
            'SELECT * FROM kingdom WHERE username = "' . clean($u) . '";'
        );
    }
}