<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Prices extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        $k = $this->get_kingdom(clean($user));
        $buyprices = $this->calculate_prices($k, false);
        $sellprices = $this->calculate_prices($k, true);

        $report = "prices:\n";
        foreach($buyprices as $i => $v) {
            $report .= $i . ": " . $v . ($sellprices[$i] == $v ? " gc\n" : " (sell: " . $sellprices[$i] . ") gc\n");
        }
        return $this->__communicator->sendReply($this->__message->getAuthorName(), $report);
    }
}