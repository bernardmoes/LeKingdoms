<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Players extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $result = $this->db->query("SELECT username FROM kingdom WHERE locations <> \"\";");

        $kingdoms = array();
        if ($result->num_rows > 0) {
            while($kingdom = $result->fetch_assoc()) {
                $kingdoms[] = $kingdom['username'] ;
            }
        }
        return $this->reply($user,$p, "currently active kingdoms: " . implode(", ", $kingdoms));

    }
}