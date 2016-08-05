<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class SellAll extends Trade
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function sellAll()
    {
        $u = clean($u);
        $item = clean($item);

        if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

        $k = $this->get_kingdom(clean($u));


        $t = $this->item_translate($item);


        $amount = $k[$t];

        if ($amount == 0) return "you have no " . $item . " to sell.";

        return $this->trade($u, "sell", $amount, $item);
    }


    function execute()
    {
        $k = $this->get_kingdom($user);
        if ($k['TC'] < 1) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you cannot trade until you have at least one trading center");
        if (count($c) != 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "try !sellall wood");

        return $this->__communicator->sendReply($this->__message->getAuthorName(), $this->sellall($user, clean($c[1])));
    }
}