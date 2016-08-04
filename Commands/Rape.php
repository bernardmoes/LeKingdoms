<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Rape extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        if (count($c) != 2) return 	$this->reply($user,$p, "you mean !rape fendle?");

        $k = $this->get_kingdom(clean($c[1]));
        $lostsold = $k['S'];
        $lostbattlements = $k['BT'];
        $lostpop = round($k['P'] / 2);


        $k['S'] = 0;
        $k['BT'] = 0;
        $k['P'] -= $lostpop;
        if ($k['P'] < 5) $k['P'] = 5;

        $this->room("a mysterious raping force appears at " . $c[1] . "'s kingdom. " . $lostsold . " soldiers have their bottoms violated and subsequently hobble from the kingdom never to be seen again. as a result " . $lostbattlements . " battlements fell apart and will need to be replaced. many civilians were also unpleasantly greeted in their bedrooms resulting in " . $lostpop . " fleeing the castle.");

        $this->save_kingdom($k);
    }
}