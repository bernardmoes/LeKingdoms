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

KingdomHelper::$buildings_lookup = array_flip(KingdomHelper::$buildings_key);

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







