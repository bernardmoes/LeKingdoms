<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once "DBCommunicator.php";
require_once "Command.php";
require_once "Communicators/DiscordCommunicator.php";
require_once "Helpers/KingdomHelper.php";
foreach (glob("Commands/*.php") as $filename)
{
    require_once $filename;
}

foreach (glob("Commands/Trade/*.php") as $filename)
{
    require_once $filename;
}

require_once "CommandEvaluator.php";
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 17:28
 */

class Kingdom {
    public $channel;
    public $glochannel;
    public $lastturn;

    /** @var CommandEvaluator */
    private $_commandEvaluator;
    /** @var DBCommunicator */
    private $_db;
    private $_kingdom;
    /** @var Communicator */
    private $_communicator;
    public function __construct(DiscordMessage $input, $channel, $glochannel) {
        $db = DBCommunicator::getInstance();
        $kingdom = $db->getKingdom($input->getAuthorName());
        $this->_db = $db;
        $this->channel = $channel;
        $this->glochannel = $glochannel;
        $this->_kingdom = $kingdom;
        $communicator = new DiscordCommunicator($channel, $glochannel);
        $this->_communicator = $communicator;
        $this->_commandEvaluator = new CommandEvaluator($kingdom, $communicator, $input);
    }

    public static $buildings;
    public static $buildings_key;
    public static $buildings_lookup;

    public static $spells;


    public function raze($u, $b, $a) {
        //execute raze command
    }


    // user, buildingid, amount, returns a message about the build
    public function build($u, $b, $a) {
        //execute build command
    }


    public function buildmax($u, $b) {
        //execute buildmax command
    }


    public function autoannex($u) {
        //execute auto annex command
    }

    public function obliterate($u, $cloc) {
        //execute obliterate command
    }

    public function annex($u, $cloc) {
        //execute annex command
    }

    public function turn() {
        //execute turn kingdom command
    }

    public function attack($loc, $attacker) {
        //attack command
    }


    public function trade($u, $bs, $amount, $item) {
        //execute trade command
    }


    public function buymax($u, $item) {
        //execute buy max command
    }

    public function sellall($u, $item) {
       //execute sell all command
    }

    public function turn_remove_old_spells() {
        $this->_db->executeQuery("UPDATE spells SET duration = duration - 1;");
        $this->_db->executeQuery("DELETE FROM spells WHERE duration <= 0;");
    }

    public function  cast($spell, $victim, $attacker) {
        //execute cast spell
    }

    public function espionage($loc, $u) {
        //execute spy command
    }

    public function thieve($loc, $u) {
       //execute steal command
    }

    public function yolo($u) {
        // execute yolo command
    }

    public function processCommand(DiscordMessage $message) {
        $this->_commandEvaluator->evaluateCommand();
    }

}