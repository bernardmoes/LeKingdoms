<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class BuyMax extends Trade
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function buyMax()
    {
        $u = clean($u);
        $item = clean($item);

        if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

        $k = $this->get_kingdom(clean($u));
        $prices = $this->calculate_prices($k, !$b);


        if (!isset($prices[$item]))  {
            if (!isset($prices[$item . 's']))  {
                return "invalid item specified: " . $item . ". you can choose from " . implode(", ", array_keys($prices));
            } else $item .= 's';
        }

        if ($prices[$item] <= 0) return "cannot buymax, error 1334";
        $t = $this->item_translate($item);


        $amount = 32767 - $k[$t];

        $tmp = round($k['G'] / $prices[$item]);

        if ($tmp < $amount) $amount = $tmp;

        if ($amount == 0) return "cannot buy even one " . $item . "; " . $this->trade($u, "buy", 1, $item);

        return $this->trade($u, "buy", $amount, $item);
    }

    function execute()
    {
        $k = $this->get_kingdom($user);
        if ($k['TC'] < 1) return $this->reply($user,$p, "you cannot trade until you have at least one trading center");
        if (count($c) != 2) return $this->reply($user,$p, "try !buymax wood");

        return $this->reply($user,$p, $this->buymax($user, clean($c[1])));
    }
}