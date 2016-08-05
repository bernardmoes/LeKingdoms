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
        $this->_db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD) or die("can't connect mysql");
    }

    public static function getInstance()
    {
        if(DBCommunicator::$_instance == null)
        {
            DBCommunicator::$_instance = new DBCommunicator();
        }
        return DBCommunicator::$_instance;
    }

    public function executeQuery($sql)
    {
        $sth = $this->_db->prepare($sql);
        $sth->execute();
        return $sth;
    }

    public function getKingdom($u) {
        return $this->executeQuery('SELECT * FROM kingdom WHERE username = "' . clean($u) . '";')->fetch(PDO::FETCH_ASSOC);;
    }

    public function saveKingdom($k) {
        $updates = array();
        foreach($k as $key => $v) {

            if ($key != "username" && $key != "locations"){
                if ($v < 0) $v = 0;
                $updates[] = $key . "=" . round($v);
            }
        }

        $updates[] = "locations = \"" . implode(",", array_unique(KingdomHelper::make_loc_array(clean($k['locations'])))) . "\"";

        $updateq = "UPDATE kingdom SET " . implode(", ", $updates) . " WHERE username = \"" . clean($k['username']) . "\" LIMIT 1;";
        $this->executeQuery($updateq);

    }
}