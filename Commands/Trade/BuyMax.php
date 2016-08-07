<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class BuyMax extends Trade
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function buyMax($u, $item)
    {
        $u = clean($u);
        $item = clean($item);

        if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

        $k = $this->__db->getKingdom($u);
        $prices = KingdomHelper::calculate_prices($k, false);


        if (!isset($prices[$item]))  {
            if (!isset($prices[$item . 's']))  {
                return "invalid item specified: " . $item . ". you can choose from " . implode(", ", array_keys($prices));
            } else $item .= 's';
        }

        if ($prices[$item] <= 0) return "cannot buymax, error 1334";
        $t = KingdomHelper::item_translate($item);


        $amount = 32767 - $k[$t];

        $tmp = round($k['G'] / $prices[$item]);

        if ($tmp < $amount) $amount = $tmp;

        if ($amount == 0) return "cannot buy even one " . $item . "; " . parent::tradeNow($u, "buy", 1, $item);

        return parent::tradeNow($u, "buy", $amount, $item);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        $k = $this->__kingdom;
        if ($k['TC'] < 1) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you cannot trade until you have at least one trading center");
        if (count($c) != 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "try !buymax wood");

        return $this->__communicator->sendReply($this->__message->getAuthorName(), $this->buymax($this->__message->getAuthorName(), clean($c[1])));
    }
}