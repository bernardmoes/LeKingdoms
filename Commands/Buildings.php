<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class Buildings extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        $report = "buildings:\n";

        foreach (self::$buildings_key as $k => $v) {
            $costs = array();
            if (self::$buildings[$k]['wo'] > 0) $costs[] = self::$buildings[$k]['wo'] . " wood";
            if (self::$buildings[$k]['r'] > 0) $costs[] = self::$buildings[$k]['r'] . " stone";
            if (self::$buildings[$k]['i'] > 0) $costs[] = self::$buildings[$k]['i'] . " iron";
            if (self::$buildings[$k]['g'] > 0) $costs[] = self::$buildings[$k]['g'] . " gc";
            $report .= $v . " ( " .  implode(", ", $costs) . ' ) : ' . self::$buildings[$k]['d'] . ".\n";
        }
        $this->reply($user,$p,$report);
    }
}