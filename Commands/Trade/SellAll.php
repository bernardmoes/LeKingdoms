<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class SellAll extends Trade
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function sellAll($u, $item)
    {
        $u = clean($u);
        $item = clean($item);

        if ($item == 'faggot' || $item == 'faggots') $item = 'wood';

        $k = $this->__db->getKingdom($u);


        $t = KingdomHelper::item_translate($item);


        $amount = $k[$t];

        if ($amount == 0) return "you have no " . $item . " to sell.";

        return parent::tradeNow($u, "sell", $amount, $item);
    }


    function execute()
    {
        $c = $this->__message->getContentArgs();
        $k = $this->__kingdom;
        if ($k['TC'] < 1) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you cannot trade until you have at least one trading center");
        if (count($c) != 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "try !sellall wood");

        return $this->__communicator->sendReply($this->__message->getAuthorName(), $this->sellall($this->__message->getAuthorName(), clean($c[1])));
    }
}