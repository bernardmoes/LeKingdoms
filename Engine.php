<?php
$turnonly =  false;

include __DIR__.'/vendor/autoload.php';

require_once "DBCommunicator.php";
require_once "config.inc.php";
require_once "Models/DiscordMessage.php";
require_once "Kingdom.php";


use Discord\Discord;

/****
 ***** KINGDOMS - A GAME BY SYTHE.ORG
 ****/
error_reporting(E_ALL);

function clean($input) {
    return preg_replace('/[^a-zA-Z0-9\#\-\:\,]/m', '', $input);

}
function clean_note($input) {
    return preg_replace('/[^a-zA-Z0-9\-\#\:\ \.\,]/m', '', $input);
}


Kingdom::$buildings_key = array(
    "B" => "bank",
    "FA" => "farm",
    "MN" => "mine",
    "FR" => "forest",
    "SM" => "smelter",
    "BT" => "battlements",
    "U" => "military school",
    "PR" => "priesthood",
    "WF" => "weapons factory",
    "BK" => "barracks",
    "TC" => "trade center",
    "H" => "house",
    "T" =>	"thieves den",
    "IA" => "intelligence agency",
    "D" => "dam",
    "ST" => "stable",
    "Q" => "quarry",
    "TI" => "technical institute",
    "SI" => "siege tower",
    "WM" => "war machine"
);

Kingdom::$buildings = array(
    "B" => 	array("wo" => 10, "r" => 0, "i"=> 10,  "l" => 2,	"g" => 100,	"d" => "generates income"),
    "FA" => 	array("wo" => 10, "r" => 0, "i" => 0, "l" => 10,	"g" => 50,	"d" => "generates food"),
    "MN" => 	array("wo" => 5, "r" => 0, "i" => 0, "l" => 5,	"g" => 100,	"d" => "generates iron"),
    "FR" => 	array("wo" => 0, "r" => 0, "i" => 0, "l" => 10,	"g" => 30,	"d" => "generates wood, and some food"),
    "SM" => 	array("wo" => 10, "r" => 10, "i" => 0, "l" => 2,	"g" => 20,	"d" => "boosts mining, weapons factories"),
    "BT" => 	array("wo" => 20, "r" => 20, "i" => 10, "l" => 2,	 "g" => 25,	"d" => "increases the kingdom's defences"),
    "U" => 	array("wo" => 20, "r" => 0, "i" => 0, "l" => 10,	 "g" => 50,	"d" => "increases your army's prowess"),
    "PR" => 	array("wo" => 20, "r" => 0, "i" => 0, "l" => 5,	 "g" => 60,	"d" => "generates magical runes"),
    "WF" =>	array("wo" => 10, "r" => 0, "i" => 0, "l" => 2,	 "g" => 50,	"d" => "generates weapons from iron"),
    "BK" => 	array("wo" => 10, "r" => 0, "i" => 0, "l" => 5,	 "g" => 100,	"d" => "houses and trains soldiers"),
    "TC" =>	array("wo" => 40, "r" => 0, "i" => 10, "l" => 20, 	"g" => 50,	"d" => "allows you to !trade commodities"),
    "H" =>		array("wo" => 5, "r" => 0, "i" => 0, "l" => 5, 	"g" => 40,	"d" => "generates population"),
    "T" =>	array("wo" => 5, "r" => 0, "i" => 0, "l" => 1, 	"g" => 20,	"d" => "allows you to thieve from other kingdoms"),
    "IA" => array("wo" => 20, "r" => 5, "i" => 0, "l" => 2, "g" => 100, "d" => "gathers information on other kingdoms"),
    "D" => array("wo" => 5, "r" => 20, "i" => 0, "l" => 30, "g" => 100, "d" => "creates water for your population"),
    "ST" => array("wo" => 15, "r" => 5, "i" => 0, "l" => 20, "g" => 75, "d" => "generates and stables horses"),
    "Q" => array("wo" => 50, "r" => 0, "i" => 0, "l" => 15, "g" => 40, "d" => "generates stone"),
    "TI" => array("wo" => 50, "r" => 5, "i" => 5, "l" => 10, "g" => 50, "d" => "increases space efficiency"),
    "SI" => array("wo" => 5000, "r" => 100, "i" => 1000, "l" => 5, "g" => 5000, "d" => "advanced attack unit, increases attack rating"),
    "WM" => array("wo" => 32767, "r" => 32767, "i" => 32767, "l" => 1000, "g" => 50000000, "d" => "war machine... for obliterating opponents")
);

Kingdom::$buildings_lookup = array_flip(Kingdom::$buildings_key);

Kingdom::$spells = array (
    "drought" => array("r" => 50, "l" => 1, "d" => "decreases the productivity of enemy farms"),
    "rain" => array("r" => 10, "l" => 1, "d" => "increases the productivity of farms and forests and decreases damage from fires"),
    "plague" => array("r" => 50, "l" => 1, "d" => "kills a random percentage of the enemy population"),
    "fire" => array("r" => 50, "l" => 1, "d" => "razes a random number of enemy buildings to the ground"),
    /*		"shield" => array("r" => 300, "l" => 1, "d" => "defends against attacks for one round"),*/
    "protection" => array("r" => 3000, "l" => 3, "d" => "defends against attacks for three rounds"),
    "health" => array("r" => 20, "l" => 2, "d" => "defends against plagues"),
    "wardance" => array("r" => 3000, "l" => 1, "d" => "attacks on the target can capture up to 10 squares of land")
);


$discord = new Discord([
    'token' => BOT_TOKEN
]);


$discord->on('ready', function ($discord) {
    $discord->on('message', function ($message, $discord) {
        $input = new DiscordMessage($message);
        if ($input->shouldProcess()) {
            $guild = $discord->guilds->first();
            $glochannel = null;
            foreach ($guild->channels as $c) {
                //	echo "chan: " . $c->id;
                if ($c->id == '210770776570331146') {
                    $glochannel = $c;
                    break;
                }
            }

            if ($message->content == '!map') {
                $message->reply("Map is here: http://img.sythe.org/kingdomsmap.php although ShinBot will do a quick image for you if you ask !m nicely");
                return;
            }

            $bot = new Kingdom($input, $message->channel, $glochannel);
            //		$message->reply('c');
            $username = "";
            $row = DBCommunicator::getInstance()->executeQuery("select username from discord_player where guid = UNHEX('" . bin2hex('' . $message->author->id) . "');")->fetch(PDO::FETCH_ASSOC) or print(mysql_error());
            if ($row === FALSE) {
                DBCommunicator::getInstance()->executeQuery("insert into discord_player (guid, username) values ( UNHEX('" . bin2hex(''.$message->author->id) . "'), UNHEX('" . bin2hex($message->author->username) . "'));") or print(mysql_error());
                $row = DBCommunicator::getInstance()->executeQuery("select username from discord_player where guid = UNHEX('" . bin2hex(''.$message->author->id) . "');")->fetch(PDO::FETCH_ASSOC)  or print(mysql_error());
            }
            if (!array_key_exists('username', $row)) {
                $message->reply("Not sure who you are <@" . $message->author->id . ">");
                return;
            }

            $bot->processCommand($input);
        }
    });
});

$discord->run();


?>







