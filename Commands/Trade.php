<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Trade extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function tradeNow()
    {
        $u = clean($u);
        $b = ($bs == "buy");

        if ($amount < 0) {
            $b = !$b;
            $amount = abs($amount);
        }

        $amount = intval($amount);
        $item = clean($item);

        if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

        $k = $this->get_kingdom(clean($u));
        $prices = $this->calculate_prices($k, !$b);



        if (!isset($prices[$item]))  {
            if (!isset($prices[$item . 's']))  {
                return "invalid item specified: " . $item . ". you can choose from " . implode(", ", array_keys($prices));
            } else $item .= 's';
        }
        $basecost = $amount * $prices[$item];

        $purchasecost = round($basecost * 1.00);
        $salecost = round($basecost *0.9);

        if ($b &&  $purchasecost > $k['G']) return "you do not have enough money for this trade! ";

        $t = $this->item_translate($item);
        if ($t === false) return "invalid item specified";

        if (!$b && $amount > $k[$t]) return "you do not have enough " . $item . " to sell that much!";

        // if we are here then they can cover their order

        if ($b) {

            if ($k[$t] + $amount > 32767) return "buying this many "  . $this->translate($t) . " would exceed the unit cap of 32767.";
            $k[$t] += $amount;
            $k['G'] -= $purchasecost;
        } else {
            $k[$t] -= $amount;
            $k['G'] += $salecost;
        }

        $this->save_kingdom($k);
        return ($b ? "bought " : "sold ") . $amount . " " . $item . ". you have " . $k['G'] . " gc in your coffers";
    }

    function execute()
    {
        if ($c[0] != 'trade') array_unshift($c, 'trade');
        $k = $this->get_kingdom($user);
        if ($k['TC'] < 1) return $this->reply($user,$p, "you cannot trade until you have at least one trading center");
        if (count($c) < 4) return $this->reply($user,$p, "specify an item to trade using like so: !trade sell 10 wood. you can buy and sell food, water, wood, soldiers and stone.");
        if (!($c[1] == 'buy' || $c[1] == 'sell')) return $this->reply($user,$p, "you can't " . $c[1] . " you can only buy or sell. try for example: !trade buy 2 food");

        if (intval($c[2]) . "" == $c[2]) {
            $this->reply($user,$p, $this->trade($user, $c[1], $c[2], $c[3]));
        } else {
            $this->reply($user,$p, $this->trade($user, $c[1], $c[3], $c[2]));

        }
    }
}