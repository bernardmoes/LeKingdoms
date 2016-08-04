<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Items extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $result = $this->db->query("SELECT item, amountleft FROM items WHERE kingdom = \"" . clean($user) . "\";");
        $report = '';

        if ($result && $result->num_rows > 0) {
            $report .= "your kingdom has the following magical items in its possession:\n";
            while($notes = $result->fetch_assoc())  $report .=  $notes['item'] . ' with ' . $notes['amountleft'] . ' uses remaining'. "\n";

        } else {
            $report .= "your kingdom possesses no magical items at this time.";

        }
        return $this->reply($user,$p, $report);
    }
}