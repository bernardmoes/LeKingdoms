<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class SellAll extends Trade
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
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
        if ($k['TC'] < 1) return $this->reply($user,$p, "you cannot trade until you have at least one trading center");
        if (count($c) != 2) return $this->reply($user,$p, "try !sellall wood");

        return $this->reply($user,$p, $this->sellall($user, clean($c[1])));
    }
}