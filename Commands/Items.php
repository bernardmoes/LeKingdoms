<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Items extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
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
        return $this->__communicator->sendReply($this->__message->getAuthorName(), $report);
    }
}